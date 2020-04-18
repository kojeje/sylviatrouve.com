<?php
  /**
   * template name: toile
   * Template Post Type:  toiles
   */



//OBLIGATOIRE : Récupère les variables globales de Wordpress
  $context = Timber::get_context();

// on récupère le contenu du post actuel grâce à TimberPost
  $post = new TimberPost();

// on ajoute la variable post (qui contient le post) à la variable
// qu'on enverra à la vue twig
  $context['post'] = $post;

  $expos = [
    'post_type' => 'expos',
    'orderby' => 'debut',
    'order' => 'ASC'
    ];
  $lieux = [
    'post_type' => 'places',
    'orderby' => 'cp',
    'order' => 'ASC'
  ];
  $collections = [
    'post_type' => 'collections',
    'orderby' => 'post_title',
    'order' => 'ASC'
  ];
  $contacts = [
    'post_type' => 'contacts'
  ];
  $context['lieux'] = Timber::get_posts($lieux);
  $context['expos'] = Timber::get_posts($expos);
  $context['collections'] = Timber::get_posts($collections);
  $context['contacts'] = Timber::get_posts($contacts);
 
// appelle la vue twig "template-spectacles.twig" située dans le dossier views
// en lui passant la variable $context qui contient notamment ici les articles
  Timber::render('pages/templates/template-toile.twig', $context);