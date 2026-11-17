<?php
/**
 * Rendu du bloc « Infos du magasin » : carte vert sapin.
 * Styles : .bs-sapin-card dans assets/css/base.css.
 */

$bloomandsun_store = bloomandsun_store_info();
$bloomandsun_attrs = get_block_wrapper_attributes( [ 'class' => 'bs-sapin-card bs-store-card' ] );
?>
<div <?php echo $bloomandsun_attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php if ( $bloomandsun_store['open_days'] ) : ?>
		<p class="bs-chip bs-chip--open"><?php echo esc_html( $bloomandsun_store['open_days'] ); ?></p>
	<?php endif; ?>

	<h2 class="wp-block-heading">Bloom <em>&amp;</em> Sun</h2>

	<p class="bs-store-card__tagline">
		🌸 <?php echo esc_html( $bloomandsun_store['tagline'] ); ?><br>
		✨ <?php echo esc_html( $bloomandsun_store['baseline'] ); ?>
	</p>

	<p class="bs-address"><?php echo esc_html( $bloomandsun_store['street'] ); ?><br><?php echo esc_html( $bloomandsun_store['city'] ); ?></p>

	<?php if ( $bloomandsun_store['hours'] ) : ?>
		<div class="bs-hours">
			<?php foreach ( $bloomandsun_store['hours'] as [ $bloomandsun_days, $bloomandsun_time ] ) : ?>
				<p class="bs-hours__day"><?php echo esc_html( $bloomandsun_days ); ?></p>
				<p class="bs-hours__time"><?php echo esc_html( $bloomandsun_time ); ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="wp-block-buttons bs-actions">
		<div class="wp-block-button bs-btn-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bloomandsun_store['directions'] ); ?>" target="_blank" rel="noreferrer noopener">Itinéraire</a></div>
		<?php if ( $bloomandsun_store['instagram'] ) : ?>
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bloomandsun_store['instagram'] ); ?>" target="_blank" rel="noreferrer noopener">Instagram</a></div>
		<?php endif; ?>
	</div>
</div>
