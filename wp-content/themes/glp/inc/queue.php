<?php
/**
 * Scripts and stylesheets
 */


function theme_queue() {

	// Reregister WordPress default jQuery as our own
	wp_deregister_script('jquery');
	wp_register_script('jquery', get_stylesheet_directory_uri() . '/dist/jquery.js', array(), filemtime(get_stylesheet_directory() . '/dist/jquery.js'), false);

	// Pre-compiled via Grunt (see Gruntfile.js)
	wp_enqueue_style('main', get_stylesheet_directory_uri() . '/dist/main.css', array(), filemtime(get_stylesheet_directory() . '/dist/main.css'));
	wp_enqueue_script('main', get_stylesheet_directory_uri() . '/dist/main.js', array('jquery'), filemtime(get_stylesheet_directory() . '/dist/main.js'), true);

	// Localize glpAjax.ajaxurl
	wp_localize_script('main', 'glpAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

	// Async scripts
	wp_register_script('addthis', '//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-510832576c1fd9d6', false, null, true);

}
add_action('wp_enqueue_scripts', 'theme_queue', 99);