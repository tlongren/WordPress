<?php
if (extension_loaded(getenv('NEWRELIC_APPNAME'))) {
  newrelic_set_appname();
}

/*
* load_scripts
*
* load up css and javascript for tracking services and other custom stuff.
*/
function load_scripts() {
    // load styles
    if ( is_child_theme() ) {
      wp_enqueue_style( 'parent-theme', trailingslashit( get_template_directory_uri() ) . 'style.css'  );
    }
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style ( 'fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );

    // load javascript


}
add_action( 'wp_enqueue_scripts', 'load_scripts' );

/*
* Set ogp.me html prefix for opengraph
*/
function html_ogp() {

    echo 'prefix="og: http://ogp.me/ns#"';

}

/*
* apply opengraph where necessary or helpful
*/
if ( is_page() || is_home() || is_single() || is_category() || is_archive() || is_search() ) {
    function add_opengraph_markup() {
      if (is_single()) {
        global $post;
        if(get_the_post_thumbnail($post->ID, 'thumbnail')) {
          $thumbnail_id = get_post_thumbnail_id($post->ID);
          $thumbnail_object = get_post($thumbnail_id);
          $image = $thumbnail_object->guid;
        } else {
          // set default image
          $image = ''; //
        }
        //$description = get_bloginfo('description');
        $description = substr(strip_tags($post->post_content),0,200) . '...';
    ?>
    <meta property="og:title" content="<?php the_title(); ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:image" content="<?php echo $image; ?>" />
    <meta property="og:url" content="<?php the_permalink(); ?>" />
    <meta property="og:description" content="<?php echo $description ?>" />
    <meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />

    <?php
      }
    }
    add_action('wp_head', 'add_opengraph_markup');
}

/*
* Add schema.org markup where helpful
*/
function schema_org_markup() {
	$schema = 'http://schema.org/';
	// Is single post
	if ( is_single() ) {
		$type = "Article";
	} // Contact form page ID
	else {
		if ( is_page( 1 ) ) {
			$type = 'ContactPage';
		} // Is author page
		elseif ( is_author() ) {
			$type = 'ProfilePage';
		} // Is search results page
		elseif ( is_search() ) {
			$type = 'SearchResultsPage';
		} // Is of movie post type
		elseif ( is_singular( 'movies' ) ) {
			$type = 'Movie';
		} // Is of book post type
		elseif ( is_singular( 'books' ) ) {
			$type = 'Book';
		}
    elseif ( function_exists(is_woocommerce) && is_woocommerce() ) {
      $type = 'Product';
    } else {
			$type = 'WebPage';
		}
	}
	echo 'itemscope="itemscope" itemtype="' . $schema . $type . '"';
}