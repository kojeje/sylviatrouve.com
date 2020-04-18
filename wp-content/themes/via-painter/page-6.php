<?php

//OBLIGATOIRE : Récupère les variables globales de Wordpress
  $context = Timber::get_context();

// on récupère le contenu du post actuel grâce à TimberPost
  $post = new TimberPost();

// on ajoute la variable post (qui contient le post) à la variable
// qu'on enverra à la vue twig
  $context['post'] = $post;



// tableau d'arguments pour modifier la requête en base
// de données, et venir récupérer uniquement les trois
// derniers articles



$args_peintures =[
  'post_type' => 'toiles',
  'meta_query' => [
    'relation' => 'AND',
    [
      'key' =>'type',
      'value' => 'peinture',
      'compare' => 'LIKE'
    ],
    [
      'key' =>'technique_de_peinture',
      'value' => 'huile',
      'compare' => 'LIKE'
    ],

  ],
  'meta_key' =>'note',
  'orderby' => [

    'note'=> 'DESC'
  ],
  'posts_per_page' => 5
];
$args_dessins =[
  'post_type' => 'toiles',
  'meta_query' => [
    'relation' => 'AND',
    [
      'key' =>'type',
      'value' => 'dessin/croquis',
      'compare' => 'LIKE'
    ],
    [
      'key' =>'technique_croquis',
      'value' => 'encre',
      'compare' => 'LIKE'
    ],
    [
      'key' =>'format',
      'value' => 'oeuvre',
      'compare' => 'LIKE'
    ],

    ],
    'meta_key' =>'note',
    'orderby' => [

      'note'=> 'DESC'
    ],
    'posts_per_page' => 5
  ];
  $args_temperas =[
    'post_type' => 'toiles',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' =>'type',
        'value' => 'temperas',
        'compare' => 'LIKE'
      ],
      [
        'key' =>'format',
        'value' => 'oeuvre',
        'compare' => 'LIKE'
      ],

    ],
    'meta_key' =>'note',
    'orderby' => [

      'note'=> 'DESC'
    ],
    'posts_per_page' => 5
  ];
  $args_carnets =[
    'post_type' => 'toiles',
    'meta_query' => [
      'relation' => 'AND',

      [
        'key' =>'format',
        'value' => 'carnet/reperage',
        'compare' => 'LIKE'
      ],

    ],
    'meta_key' =>'note',
    'orderby' => [

      'note'=> 'DESC'
    ],
    'posts_per_page' => 5
  ];

  $args_expos =[
    'post_type' => 'expos',
    'meta_key' => 'debut',
    'order' => 'ASC',
    'orderby' => 'debut',
    'posts_per_page' => 10
  ];
  $args_ateliers =[
    'post_type' => 'ateliers',
    'order' => 'ASC',
    'orderby' => 'debut',
    'posts_per_page' => 100
  ];
  $args_stages =[
    'post_type' => 'stages',
    'order' => 'ASC',
    'orderby' => 'debut',
    'posts_per_page' => 100
  ];
  $args_contacts =[
    'post_type' => 'contacts',
  ];
  $args_actus =[
    'post_type' => 'actus',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 4
  ];

//// récupère les articles en fonction du tableau d'argument $args_posts
//// en utilisant la méthode de Timber get_posts
//// puis on les enregistre dans l'array $context sous la clé "posts"

  $context['dessins'] = Timber::get_posts($args_dessins);
  $context['peintures'] = Timber::get_posts($args_peintures);
  $context['temperas'] = Timber::get_posts($args_temperas);
  $context['carnets'] = Timber::get_posts($args_carnets);
  $context['expos'] = Timber::get_posts($args_expos);
  $context['ateliers'] = Timber::get_posts($args_ateliers);
  $context['stages'] = Timber::get_posts($args_stages);
  $context['contacts'] = Timber::get_posts($args_contacts);
  $context['actus'] = Timber::get_posts($args_actus);
  $context['url'] = $_SERVER["REQUEST_URI"];


// appelle la vue twig "page-7.twig" située dans le dossier views
// en lui passant la variable $context qui contient notamment ici les articles
  Timber::render('pages/page-6.twig', $context);
