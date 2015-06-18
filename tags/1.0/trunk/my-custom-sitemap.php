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

	protected $plugin_slug;

        private static $instance;

        protected $templates;

        public static function get_instance() {

                if( null == self::$instance ) {
                        self::$instance = new CustomSitemapTemplate();
                } 

                return self::$instance;

        } 

        private function __construct() {

                $this->templates = array();

                add_filter(
			'page_attributes_dropdown_pages_args',
			 array( $this, 'register_cst_plugin_template' ) 
		);

                add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_cst_plugin_template' ) 
		);

                add_filter(
			'template_include', 
			array( $this, 'cst_plugin_template') 
		);

                $this->templates = array(
                        'custom-sitemap-template.php'     => 'Custom Sitemap',
                );

		add_action( 'wp_enqueue_scripts', 'wp_sitemap_add_styles' );

		add_action( 'admin_enqueue_scripts', 'wp_sitemap_admin_add_styles' );
				
        } 

        public function register_cst_plugin_template( $atts ) {

                $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		$templates = wp_get_theme()->get_page_templates();
                if ( empty( $templates ) ) {
                        $templates = array();
                } 

                wp_cache_delete( $cache_key , 'themes');

                $templates = array_merge( $templates, $this->templates );

                wp_cache_add( $cache_key, $templates, 'themes', 1800 );

                return $atts;

        } 

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
				
                if( file_exists( $file ) ) {
                        return $file;
                } 
		else { 
			echo $file; 
		}

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
