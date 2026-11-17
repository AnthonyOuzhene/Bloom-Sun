<?php
/**
 * Bloom & Sun — point d'entrée du thème.
 *
 * Répartition :
 * - Affichage : templates/ et parts/ (fichiers .html, balisage de blocs uniquement)
 * - Logique   : inc/ (PHP uniquement, aucun HTML)
 * - Vues du back-office : views/ (affichage uniquement, données préparées dans inc/)
 * - Styles    : assets/css/
 */

require_once __DIR__ . '/inc/setup.php';       // prise en charge du thème, vues
require_once __DIR__ . '/inc/permissions.php'; // super administrateur, administrateur, gestionnaire
require_once __DIR__ . '/inc/assets.php';      // chargement des CSS et du script d'éditeur
require_once __DIR__ . '/inc/settings.php';    // moteur des pages de réglages du back-office
require_once __DIR__ . '/inc/store.php';       // menu « Infos du magasin »
require_once __DIR__ . '/inc/home.php';        // menu « Page d'accueil » (idées cadeaux, collections)
require_once __DIR__ . '/inc/products.php';    // fiche produit : case « Idée cadeau »
require_once __DIR__ . '/inc/bindings.php';    // données injectées dans les gabarits
require_once __DIR__ . '/inc/editor.php';      // éditeurs et extensions
