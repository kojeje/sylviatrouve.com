<?php
  /**
   * template name: expo
   * Template Post Type:  expos
   */



//OBLIGATOIRE : Récupère les variables globales de Wordpress
  $context = Timber::get_context();

// on récupère le contenu du post actuel grâce à TimberPost
$post = new TimberPost();

// on ajoute la variable post (qui contient le post) à la variable
// qu'on enverra à la vue twig
$context['post'] = $post;

$contact = [
  'post_type' => 'contacts',

];

$context['contacts'] = Timber::get_posts($contact);
// appelle la vue twig "template-spectacles.twig" située dans le dossier views
// en lui passant la variable $context qui contient notamment ici les articles
  Timber::render('pages/templates/template-expo.twig', $context);
