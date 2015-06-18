<?php
/*
Plugin Name: Custom Sitemap Template
Description: Plugin provides a custom sitemap template. You can fully customize your sitemap using plugin settings.
Plugin URI: http://www.divyanshiinfotech.com/
Version: 1.0
Author: Anil Meena
Author URI: http://www.anilmeena.com/
License: GPLv2
*/

require_once('lib/functions.php');

class CustomSitemapTemplate {

		/**
         * A Unique Identifier
         */
		 protected $plugin_slug;

        /**
         * A reference to an instance of this class.
         */
        private static $instance;

        /**
         * The array of templates that this plugin tracks.
         */
        protected $templates;


        /**
         * Returns an instance of this class. 
         */
        public static function get_instance() {

                if( null == self::$instance ) {
                        self::$instance = new CustomSitemapTemplate();
                } 

                return self::$instance;

        } 

        /**
         * Initializes the plugin by setting filters and administration functions.
         */
        private function __construct() {

                $this->templates = array();


                // Add a filter to the attributes metabox to inject template into the cache.
                add_filter(
					'page_attributes_dropdown_pages_args',
					 array( $this, 'register_cst_plugin_template' ) 
				);


                // Add a filter to the save post to inject out template into the page cache
                add_filter(
					'wp_insert_post_data', 
					array( $this, 'register_cst_plugin_template' ) 
				);


                // Add a filter to the template include to determine if the page has our 
				// template assigned and return it's path
                add_filter(
					'template_include', 
					array( $this, 'cst_plugin_template') 
				);


                // Add your templates to this array.
                $this->templates = array(
                        'custom-sitemap-template.php'     => 'Custom Sitemap',
                );

		add_action( 'wp_enqueue_scripts', 'wp_sitemap_add_styles' );

		add_action( 'admin_enqueue_scripts', 'wp_sitemap_admin_add_styles' );
				
        } 


        /**
         * Adds our template to the pages cache in order to trick WordPress
         * into thinking the template file exists where it doens't really exist.
         *
         */

        public function register_cst_plugin_template( $atts ) {

                // Create the key used for the themes cache
                $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

                // Retrieve the cache list. 
				// If it doesn't exist, or it's empty prepare an array
				$templates = wp_get_theme()->get_page_templates();
                if ( empty( $templates ) ) {
                        $templates = array();
                } 

                // New cache, therefore remove the old one
                wp_cache_delete( $cache_key , 'themes');

                // Now add our template to the list of templates by merging our templates
                // with the existing templates array from the cache.
                $templates = array_merge( $templates, $this->templates );

                // Add the modified cache to allow WordPress to pick it up for listing
                // available templates
                wp_cache_add( $cache_key, $templates, 'themes', 1800 );

                return $atts;

        } 

        /**
         * Checks if the template is assigned to the page
         */
        public function cst_plugin_template( $template ) {

                global $post;

                if (!isset($this->templates[get_post_meta( 
					$post->ID, '_wp_page_template', true 
				)] ) ) {
					
                        return $template;
						
                } 

                $file = plugin_dir_path(__FILE__). get_post_meta( 
					$post->ID, '_wp_page_template', true 
				);
				
                // Just to be safe, we check if the file exist first
                if( file_exists( $file ) ) {
                        return $file;
                } 
				else { echo $file; }

                return $template;

        } 

} 

function wp_sitemap_add_styles() {
	wp_register_style( 'cst-main-style', plugin_dir_url( __FILE__ ) . 'css/sitemap.css' );
	wp_enqueue_style('cst-main-style');
}

function wp_sitemap_admin_add_styles(){
	wp_register_style( 'cst-admin-style', plugin_dir_url( __FILE__ ) . 'lib/css/admin.css' );
	wp_enqueue_style('cst-admin-style');
}

?>
