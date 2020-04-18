<?php
  /**

   * If you are installing Timber as a Composer dependency in your theme, you'll need this block
   * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
   * plug-in, you can safely delete this block.
   */
  $composer_autoload = __DIR__ . '/vendor/autoload.php';
  if ( file_exists($composer_autoload) ) {
    require_once( $composer_autoload );
    $timber = new Timber\Timber();
  }

  /**
   * This ensures that Timber is loaded and available as a PHP class.
   * If not, it gives an error message to help direct developers on where to activate
   */
  if ( ! class_exists( 'Timber' ) ) {

    add_action( 'admin_notices', function() {
      echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
    });

    add_filter('template_include', function( $template ) {
      return get_stylesheet_directory() . '/static/no-timber.html';
    });
    return;
  }

  /**
   * Sets the directories (inside your theme) to find .twig files
   */
  Timber::$dirname = array( 'templates', 'views' );

  /**
   * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
   * No prob! Just set this value to true
   */
  Timber::$autoescape = false;




  function dr_adding_styles() {
    wp_enqueue_style('style', get_template_directory_uri(). '/style.css');
  }

  add_action( 'wp_enqueue_scripts', 'js_adding_styles' );

  add_theme_support( 'post-thumbnails' );

  register_nav_menus( array(
    'header-menu' => 'Header-menu',
    'footer-menu'=> 'Footer-menu'

  ) );


// fonction pour créer des variables globales, accessibles dans tous les fichiers twig
  function add_to_context($data){

    // on appelle une instance de TimberMenu avec en parametre le menu qu'on veut récupérer
    //$data['menu'] = new TimberMenu('header-menu');
    $data['menu'] = new \Timber\Menu( 'header-menu' );

    return $data;


  }
  add_action('init', 'init_remove_support',100);
  function init_remove_support(){
    $post_type = 'page';

    remove_post_type_support( $post_type, 'editor');

  }



// On ajoute le résultat de notre fonction au context de twig (contexte globale)
  add_filter( 'timber/context', 'add_to_context' );

  add_filter('acf/settings/remove_wp_meta_box', '__return_false');

//  function remove_menu_items() {
//    global $menu;
//    $restricted = [
//      __('Dashboard'),
//      __('Posts'),
//      __('Comments')
//    ];
//    end ($menu);
//    while (prev($menu)){
//      $value = explode(' ',$menu[key($menu)][0]);
//      if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
//        unset($menu[key($menu)]);}
//    }
////  }
//  add_action('admin_menu', 'remove_menu_items');
  function remove_menus(){

    remove_menu_page( 'index.php' );                  //Dashboard
    remove_menu_page( 'edit.php' );                   //Posts
//    remove_menu_page( 'upload.php' );                 //Media
//    remove_menu_page( 'edit.php?post_type=page' );    //Pages
    remove_menu_page( 'edit-comments.php' );          //Comments
//    remove_menu_page( 'themes.php' );                 //Appearance
//    remove_menu_page( 'plugins.php' );                //Plugins
//    remove_menu_page( 'users.php' );                  //Users
//    remove_menu_page( 'tools.php' );                  //Tools
//    remove_menu_page( 'options-general.php' );        //Settings

  }
  add_action( 'admin_menu', 'remove_menus' );

  // déclaration des custom post types

  function fabulle_register_post_types() {


  // CPT collections
    $labels = array(
      'name' => 'collections',
      'all_items' => 'Tous les collections',  // affiché dans le sous menu
      'singular_name' => 'collection',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le projet',
      'menu_name' => 'collections'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'rewrite'	 => array( 'slug' => 'collections'),
      'supports' => array( 'title','author', 'thumbnail', 'excerpt'),
      'menu_position' => 1,
      'menu_icon' => 'dashicons-edit',
    );
    register_post_type( 'collections', $args );

  // CPT toiles
  $labels = array(
    'name' => 'toiles',
    'all_items' => 'Toutes les toiles',  // affiché dans le sous menu
    'singular_name' => 'toiles',
    'add_new_item' => 'Nouvelle toile',
    'edit_item' => 'Modifier la toile',
    'menu_name' => 'toiles'
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'rewrite'	 => array( 'slug' => 'toiles'),
    'supports' => array( 'title','author', 'thumbnail', 'excerpt'),
    'menu_position' => 2,
    'menu-icon' => 'dashicons-admin-post'
  );
  register_post_type( 'toiles', $args );

  // CPT expo
      $labels = array(
        'name' => 'expos',
        'all_items' => 'Toutes les expos',  // affiché dans le sous menu
        'singular_name' => 'expo',
        'add_new_item' => 'Nouveau',
        'edit_item' => 'Modifier l\'expo',
        'menu_name' => 'expos'
      );
      $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite'	 => array( 'slug' => 'expo'),
        'supports' => array( 'title','author', 'thumbnail', 'excerpt'),
        'menu_position' => 3,
        'menu_icon' => 'dashicons-code-standards',
      );
      register_post_type( 'expos', $args );


    // CPT cycles
    $labels = array(
      'name' => 'cycles',
      'all_items' => 'Tous les cycles',  // affiché dans le sous menu
      'singular_name' => 'cycle',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le cycle',
      'menu_name' => 'cycles'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array( 'title','author', 'thumbnail', 'excerpt'),
      'menu_position' => 4,
      'menu_icon' => 'dashicons-admin-site-alt',
    );
    register_post_type( 'cycles', $args );

    // CPT ateliers
    $labels = array(
      'name' => 'ateliers',
      'all_items' => 'Tous les ateliers',  // affiché dans le sous menu
      'singular_name' => 'atelier',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier la référence',
      'menu_name' => 'ateliers'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array( 'title','author', 'thumbnail', 'excerpt'),
      'menu_position' => 5,
      'menu_icon' => 'dashicons-admin-site-alt',
    );
    register_post_type( 'ateliers', $args );

    // CPT stages
    $labels = array(
      'name' => 'stages',
      'all_items' => 'Tous les stages',  // affiché dans le sous menu
      'singular_name' => 'stage',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le stage',
      'menu_name' => 'stages'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array('title','author', 'thumbnail', 'excerpt'),
      'menu_position' => 6,
      'menu_icon' => 'dashicons-admin-users',
    );
    register_post_type( 'stages', $args );

    // CPT journee
    $labels = array(
      'name' => 'journees',
      'all_items' => 'Tous les journees',  // affiché dans le sous menu
      'singular_name' => 'journee',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le journee',
      'menu_name' => 'journees'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array('title','thumbnail'),
      'menu_position' => 6,
      'menu_icon' => 'dashicons-admin-users',
    );
    register_post_type( 'journees', $args );
    // CPT invités
    $labels = array(
      'name' => 'invites',
      'all_items' => 'Tous les invites',  // affiché dans le sous menu
      'singular_name' => 'invite',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le invite',
      'menu_name' => 'invites'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array('thumbnail', 'title'),
      'menu_position' => 7,
      'menu_icon' => 'dashicons-admin-users',
    );
    register_post_type( 'invites', $args );
    // CPT actu
    $labels = array(
      'name' => 'actus',
      'all_items' => 'Tous les actus',  // affiché dans le sous menu
      'singular_name' => 'actu',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le actu',
      'menu_name' => 'actus'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array('title','thumbnail', 'excerpt'),
      'taxonomies' => array('category'),
      'menu_position' => 8,
      'menu_icon' => 'dashicons-admin-users',
    );
    register_post_type( 'actus', $args );
// CPT periodes
    $labels = array(
      'name' => 'periodes',
      'all_items' => 'Toutes les periodes',  // affiché dans le sous menu
      'singular_name' => 'periode',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier la periode',
      'menu_name' => 'periodes'
    );

    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array('title','thumbnail'),
      'taxonomies' => array('category'),
      'menu_position' => 9,
      'menu_icon' => 'dashicons-admin-users',
    );
    register_post_type( 'periodes', $args );


    // CPT Places
    $labels = array(
      'name' => 'Places',
      'all_items' => 'Tous les lieux',  // affiché dans le sous menu
      'singular_name' => 'Lieu',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le lieu',
      'menu_name' => 'Lieux'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
    'supports' => array( 'title', 'author', 'thumbnail'),
      'menu_position' => 10,
      'menu_icon' => 'dashicons-admin-site-alt',
    );
    register_post_type( 'places', $args );


    // CPT contact
    $labels = array(
      'name' => 'Contacts',
      'all_items' => 'Tous les contacts',  // affiché dans le sous menu
      'singular_name' => 'Contact',
      'add_new_item' => 'Nouveau',
      'edit_item' => 'Modifier le contact',
      'menu_name' => 'Contacts'
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'has_archive' => true,
      'supports' => array('title','thumbnail','editor'),
      'taxonomies' => array('category'),
      'menu_position' => 11,
      'menu_icon' => 'dashicons-admin-users',
    );
    register_post_type( 'contacts', $args );

  }



  add_action( 'init', 'fabulle_register_post_types'); // Le hook init lance la fonction
  function custom_menu_order($menu_ord) {
    if (!$menu_ord) return true;
    return array(
      'index.php', // this represents the dashboard link
      'edit.php?post_type=toiles',
      'edit.php?post_type=collections', // this is a custom post type menu
      'edit.php?post_type=expos',
      'edit.php?post_type=cycles',
      'edit.php?post_type=ateliers',
      'edit.php?post_type=stages',
      'edit.php?post_type=journees',
      'edit.php?post_type=invites',
      'edit.php?post_type=actus',
      'edit.php?post_type=periodes',
      'edit.php?post_type=places',
      'edit.php?post_type=contacts',
      'edit.php', // this is the default POST admin menu
      'edit.php?post_type=page', // this is the default page menu

    );
  }
  add_filter('custom_menu_order', 'custom_menu_order');
  add_filter('menu_order', 'custom_menu_order');
  add_filter('timber/twig', function($twig) {
    $twig->addExtension(new Twig_Extension_StringLoader());

    $twig->addFilter(
      new Twig_SimpleFilter(
        'highlight',

        function($text, array $terms) {

          $highlight = array();
          foreach($terms as $term) {
            $highlight[]= '<span class="highlight">'.$term.'</span>';
          }

          return str_ireplace($terms, $highlight, $text);
        }

      )
    );

    return $twig;
  });

  function wpc_excerpt_pages() {
    add_meta_box('postexcerpt', __('Extrait'), 'post_excerpt_meta_box', 'page', 'normal', 'core');
  }
  add_action( 'admin_menu', 'wpc_excerpt_pages' );
