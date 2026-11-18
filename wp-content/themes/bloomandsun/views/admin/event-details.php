<?php
/**
 * Vue : cadre « Détails de l'événement » (écran d'édition d'un événement).
 *
 * Affichage uniquement. Données préparées par bloomandsun_event_form_data() (inc/events.php) :
 * fields (champs), images (id, url), max (nombre de photos), nonce (action).
 *
 * @var array $data
 */
?>
<?php wp_nonce_field( $data['nonce'], 'bloomandsun_event_nonce' ); ?>

<table class="form-table bs-event-form" role="presentation">
	<?php foreach ( $data['fields'] as $field ) : ?>
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

	<tr>
		<th scope="row">Photos (<?php echo (int) $data['max']; ?> maximum)</th>
		<td>
			<div class="bs-event-gallery" data-max="<?php echo (int) $data['max']; ?>">
				<input type="hidden" name="bs_event[images]" value="<?php echo esc_attr( implode( ',', wp_list_pluck( $data['images'], 'id' ) ) ); ?>" class="bs-event-gallery__input">

				<ul class="bs-event-gallery__list">
					<?php foreach ( $data['images'] as $image ) : ?>
						<li data-id="<?php echo (int) $image['id']; ?>">
							<img src="<?php echo esc_url( $image['url'] ); ?>" alt="">
							<button type="button" class="bs-event-gallery__remove" aria-label="Retirer cette photo">×</button>
						</li>
					<?php endforeach; ?>
				</ul>

				<p>
					<button type="button" class="button bs-event-gallery__add">Ajouter des photos</button>
					<span class="bs-event-gallery__count"></span>
				</p>
				<p class="description">La première photo est la plus grande dans le panneau et sur la pastille.</p>
			</div>
		</td>
	</tr>
</table>
