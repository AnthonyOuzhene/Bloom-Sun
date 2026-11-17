<?php
/**
 * Title: Logo Bloom & Sun
 * Slug: bloomandsun/logo
 * Categories: bloomandsun
 * Inserter: false
 *
 * Logo en SVG : le texte prend la couleur du lien (currentColor),
 * le soleil prend la couleur définie par --bs-logo-sun dans header.css (et footer.css pour le pied de page).
 */
?>
<!-- wp:html -->
<a class="bs-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bloom &amp; Sun, concept store — accueil">
	<svg class="bs-logo__svg" viewBox="60 395 1260 610" aria-hidden="true" focusable="false">
		<g class="bs-logo__text" fill="currentColor">
			<text x="80" y="600" font-size="205" textLength="770" lengthAdjust="spacingAndGlyphs">BLOOM</text>
			<text x="700" y="785" font-size="205" textLength="600" lengthAdjust="spacingAndGlyphs">&amp;SUN</text>
			<text class="bs-logo__baseline" x="362" y="985" font-size="54" textLength="728" lengthAdjust="spacing">CONCEPT STORE</text>
		</g>
		<g class="bs-logo__sun" fill="none" stroke-width="6" stroke-linecap="round">
			<g stroke-dasharray="64 14 8 14">
				<line x1="1261" y1="570" x2="1137" y2="596"/><line x1="1216" y1="545" x2="1153" y2="575"/><line x1="1224" y1="494" x2="1127" y2="575"/><line x1="1172" y1="493" x2="1131" y2="549"/><line x1="1156" y1="444" x2="1108" y2="561"/><line x1="1114" y1="468" x2="1102" y2="537"/><line x1="1090" y1="411" x2="1090" y2="558"/><line x1="1066" y1="468" x2="1078" y2="537"/><line x1="1024" y1="444" x2="1072" y2="561"/><line x1="1008" y1="493" x2="1049" y2="549"/><line x1="956" y1="494" x2="1053" y2="575"/><line x1="964" y1="545" x2="1027" y2="575"/><line x1="919" y1="570" x2="1043" y2="596"/>
			</g>
			<g stroke-dasharray="80 12 10 12 10 12">
				<line x1="880" y1="606" x2="1030" y2="606"/><line x1="1150" y1="606" x2="1300" y2="606"/>
			</g>
		</g>
	</svg>
</a>
<!-- /wp:html -->
