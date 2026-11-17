<?php
/**
 * Rendu du bloc « Plan du magasin » : plan OpenStreetMap (sans cookies publicitaires).
 * Styles : .bs-store-map dans assets/css/base.css.
 */

$bloomandsun_store = bloomandsun_store_info();
$bloomandsun_attrs = get_block_wrapper_attributes( [ 'class' => 'bs-store-map' ] );
?>
<figure <?php echo $bloomandsun_attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<iframe src="<?php echo esc_url( $bloomandsun_store['map_embed'] ); ?>" title="<?php echo esc_attr( 'Plan : ' . $bloomandsun_store['address'] ); ?>" loading="lazy"></iframe>
	<figcaption><a href="<?php echo esc_url( $bloomandsun_store['map_link'] ); ?>" target="_blank" rel="noreferrer noopener">Agrandir le plan</a></figcaption>
</figure>
