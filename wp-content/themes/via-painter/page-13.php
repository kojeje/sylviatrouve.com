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
  $args_toiles =[
    'post_type' => 'toiles',
    'meta_query' => [
      'relation' => 'AND',
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
    'posts_per_page' =>1000,

  ];
  $args_carnets =[
    'post_type' => 'toiles',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' =>'format',
        'value' => 'carnet/repérage',
        'compare' => 'LIKE'
      ],

    ],
    'meta_key' =>'note',
    'orderby' => [
      'note'=> 'DESC'
    ],
    'posts_per_page' =>1000,

  ];
  $args_collections =[
    'post_type' => 'collections',
    'meta_key' =>'note',
    'orderby' => [
      'note'=> 'DESC'
    ],
    'posts_per_page' =>1000,
  ];
  $args_expos = [
    'post_type' => 'expos',
    'posts_per_page' =>1000,
    ];
  $args_contacts = [
    'post_type' => 'contacts'
    ];

//// récupère les articles en fonction du tableau d'argument $args_posts
//// en utilisant la méthode de Timber get_posts
//// puis on les enregistre dans l'array $context sous la clé "posts"
  $context['toiles'] = Timber::get_posts($args_toiles);
  $context['carnets'] = Timber::get_posts($args_carnets);
  $context['collections'] = Timber::get_posts($args_collections);
  $context['expos'] = Timber::get_posts($args_expos);
  $context['contacts'] = Timber::get_posts($args_contacts);


  $context['url'] = $_SERVER["REQUEST_URI"];
//
//



// appelle la vue twig "page-13.twig" située dans le dossier views
// en lui passant la variable $context qui contient notamment ici les articles
  Timber::render('pages/page-13.twig', $context);
