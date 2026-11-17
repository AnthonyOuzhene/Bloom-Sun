<?php
/**
 * Vue : page de réglages du back-office (Infos du magasin, Page d'accueil…).
 *
 * Affichage uniquement. Les données sont préparées par bloomandsun_settings_view_data()
 * (inc/settings.php) et reçues dans $data : title, intro, group, sections (rubrique => champs).
 *
 * @var array $data
 */
?>
<div class="wrap">
	<h1><?php echo esc_html( $data['title'] ); ?></h1>
	<?php if ( $data['intro'] ) : ?>
		<p><?php echo esc_html( $data['intro'] ); ?></p>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( $data['group'] ); ?>

		<?php foreach ( $data['sections'] as $section => $fields ) : ?>
			<h2 class="title"><?php echo esc_html( $section ); ?></h2>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $field ) : ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<?php if ( 'textarea' === $field['type'] ) : ?>
								<textarea id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" rows="3" class="large-text"><?php echo esc_textarea( $field['value'] ); ?></textarea>
							<?php elseif ( 'checkbox' === $field['type'] ) : ?>
								<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="0">
								<input id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" type="checkbox" value="1" <?php checked( $field['value'], '1' ); ?>>
							<?php elseif ( 'select' === $field['type'] ) : ?>
								<select id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>">
									<?php foreach ( $field['options'] as $value => $label ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['value'], (string) $value ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<input id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" class="regular-text">
							<?php endif; ?>

							<?php if ( $field['help'] ) : ?>
								<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endforeach; ?>

		<?php submit_button( 'Enregistrer' ); ?>
	</form>
</div>
