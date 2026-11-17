<?php
/**
 * Bloom & Sun — point d'entrée du thème.
 *
 * Répartition :
 * - Affichage : templates/ et parts/ (fichiers .html, balisage de blocs uniquement)
 * - Logique   : inc/ (PHP uniquement, aucun HTML)
 * - Styles    : assets/css/
 */

require_once __DIR__ . '/inc/setup.php';    // prise en charge du thème
require_once __DIR__ . '/inc/assets.php';   // chargement des CSS et du script d'éditeur
require_once __DIR__ . '/inc/store.php';    // infos du magasin (Apparence > Personnaliser)
require_once __DIR__ . '/inc/bindings.php'; // données injectées dans les gabarits
require_once __DIR__ . '/inc/editor.php';   // éditeurs et extensions
