<?php

//OBLIGATOIRE : Récupère les variables globales de Wordpress
  $context = Timber::get_context();

// on récupère le contenu du post actuel grâce à TimberPost
  $post = new TimberPost();

// on ajoute la variable post (qui contient le post) à la variable
// qu'on enverra à la vue twig
  $context['post'] = $post;



//// tableau d'arguments pour modifier la requête en base
//// de données, et venir récupérer uniquement les trois
//// derniers articles
//
  $args_ateliers_stages =[
//
    'post_type' => ['ateliers', 'stages','cycles'],
    'meta_key' =>'debut',
    'orderby' => [

      'debut'=> 'DESC'
    ],

    'posts_per_page' =>100000,
    ];

  $args_ateliers = [

    'post_type' => 'ateliers',
    'posts_per_page' =>25,


  ];
  $args_cycles = [
    'post_type' => 'cycles',
    'meta_key' => 'debut',
   'orderby' => [

      'debut'=> 'DESC'
    ], 
  ];
  $args_stages =[
    'post_type' => 'stages',

    'posts_per_page' =>25,

  ];

  $args_contacts = [
    'post_type' => 'contacts',
    'meta_query' => [
      'key' => 'id',
      'value' => 270,
      'compare' =>'LIKE'
    ]


  ];



//// récupère les articles en fonction du tableau d'argument $args_posts
//// en utilisant la méthode de Timber get_posts
//// puis on les enregistre dans l'array $context sous la clé "posts"
  $context['ateliersstages'] = Timber::get_posts($args_ateliers_stages);
  $context['ateliers'] = Timber::get_posts($args_ateliers);
  $context['stages'] = Timber::get_posts($args_stages);
  $context['contacts'] = Timber::get_posts($args_contacts);
  $context['cycles'] = Timber::get_posts($args_cycles);
  $context['url'] = $_SERVER["REQUEST_URI"];
//
//



// appelle la vue twig "page-13.twig" située dans le dossier views
// en lui passant la variable $context qui contient notamment ici les articles
  Timber::render('pages/page-15.twig', $context);
