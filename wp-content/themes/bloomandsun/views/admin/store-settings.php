<?php
/**
 * Vue : page « Infos du magasin » du back-office.
 *
 * Affichage uniquement. Les données sont préparées par bloomandsun_store_view_data()
 * (inc/store.php) et reçues dans $data : title, intro, group, sections (rubrique => champs).
 *
 * @var array $data
 */
?>
<div class="wrap">
	<h1><?php echo esc_html( $data['title'] ); ?></h1>
	<p><?php echo esc_html( $data['intro'] ); ?></p>

	<form method="post" action="options.php">
		<?php settings_fields( $data['group'] ); ?>

		<?php foreach ( $data['sections'] as $section => $fields ) : ?>
			<h2 class="title"><?php echo esc_html( $section ); ?></h2>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $field ) : ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<input id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" class="regular-text">
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
