<?php
/*
Plugin Name: HTMLSitemap
Plugin URI: /
Description: Displays a Friendly HTML sitemap of your wordpress site.
Author: ashokkumarzx
Version: 2.0
Author URI: http://twitter.com/ashok_kumar

*/

class html_sitemap {

	function html_sitemap() {

		$this->name = 'HTML Sitemap';
		$this->version = '2.0';

		if ( !defined('WP_PLUGIN_URL') ) {
			if ( !defined('WP_CONTENT_DIR') ) define('WP_CONTENT_DIR', ABSPATH.'wp-content');
			if ( !defined('WP_CONTENT_URL') ) define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
			if ( !defined('WP_PLUGIN_DIR') ) define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
			define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
		}// end if
		
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
		add_filter('the_content', array(&$this, 'filter'));

		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", array(&$this, 'settings_link'));

	}// end function

	function deactivate() {
		delete_option('html_sitemap');
	}// end function
	
	function settings_link($links) {
		$settings_link = '<a href="options-general.php?page=/htmlsitemap/admin.php">Settings</a>';
		array_unshift($links,$settings_link);
		return $links;
	}// end function
	
	function filter($content) {

		if ( strpos($content, '<!--html-sitemap-->') !== false ) {
			$options = $this->get_options();
			$output = '<div class="html-sitemap">'."\n";			
			
			// pages			
			if ( $options['show_pages'] ) {
				if ( function_exists('exclude_pages') && $options['apply_excludes'] == false )
					remove_filter('wp_list_pages_excludes', 'exclude_pages');
				if ( count($options['excludepages']) )
					add_filter('wp_list_pages_excludes', array(&$this, 'wp_list_pages_excludes'));
				$output .= '<h3>'.stripslashes($options['title_pages']).'</h3>'."\n";
				$output .= '<ul>';
				if ( $options['show_home_page'] )
					$output .= '<li class="page_item"><a href="'.trailingslashit(get_bloginfo('url')).'" title="'.get_bloginfo('name').'" rel="nofollow">'.stripslashes($options['home_page_text']).'</a></li>'."\n";
				$output .= wp_list_pages('title_li=&sort_column=menu_order&echo=0');
				$output .= '</ul>';
			}// end if

			// posts
			if ( $options['show_posts'] ) {
				$output .= '<h3>'.stripslashes($options['title_posts']).'</h3>'."\n";
				$output .= '<ul>';
				$output .= wp_get_archives('type=postbypost&limit=1000&echo=0');
				$output .= '</ul>';
			}// end if

			// categories
			if ( $options['show_categories'] ) {
				$output .= '<h3>'.stripslashes($options['title_categories']).'</h3>'."\n";
				$output .= '<ul>';
				$output .= wp_list_categories('title_li=0&echo=');
				$output .= '</ul>';
			}// end if

			// feeds
			if ( $options['show_feeds'] ) {
				$output .= '<h3>'.stripslashes($options['title_feeds']).'</h3>'."\n";
				$output .= '<ul>';
				if ( 'open' == get_option('default_comment_status') )
					$output .= '<li><a href="'.get_bloginfo('comments_rss2_url').'" title="Comments RSS feed">Comments RSS feed</a></li>'."\n";
				$output .= '<li><a href="'.get_bloginfo('rdf_url').'" alt="RDF/RSS 1.0 feed">RDF/RSS 1.0 feed</a></li>'."\n"
					.'<li><a href="'.get_bloginfo('rss_url').'" alt="RSS 0.92 feed">RSS 0.92 feed</a></li>'."\n"
					.'<li><a href="'.get_bloginfo('rss2_url').'" alt="RSS 2.0 feed">RSS 2.0 feed</a></li>'."\n"
					.'<li><a href="'.get_bloginfo('atom_url').'" alt="Atom feed">Atom feed</a></li>';
				$output .= '</ul>';
			}// end if

			$output .= '</div><!-- /html-sitemap -->';
			
			$content = preg_replace('/<!--html-sitemap-->/ix', $output, $content);

		}// end if 
		
		return $content;
	
	}// end function 
	
	function get_options() {
		$options = get_option('html_sitemap');
		if ( !is_array($options) )
			$options = $this->set_defaults();
		return $options;
	}// end function
	
	function set_defaults() {
		$options = array(
			'show_pages' => true,
			'title_pages' => 'Main Pages',
			'show_home_page' => true,
			'home_page_text' => 'Home Page',
			'apply_excludes' => true,
			'excludepages' => array(),
			'show_posts' => true,
			'title_posts' => 'Posts & Articles',
			'show_categories' => true,
			'title_categories' => 'Categories',
			'show_feeds' => true,
			'title_feeds' => 'Feeds',
			'affiliate' => ''
		);
		update_option('html_sitemap', $options);
		return $options;
	}// end function

	function wp_list_pages_excludes() {
		$options = get_option('html_sitemap');
		return $options['excludepages'];
	}// end func

}// end class
$html_sitemap = new html_sitemap;

if ( is_admin() )
	include_once dirname(__FILE__).'/admin.php';

register_activation_hook(__FILE__, 'simple_plugin_activate');
add_action('admin_init', 'simple_plugin_redirect');

function simple_plugin_activate() {
    add_option('simple_plugin_do_activation_redirect', true);
}

function simple_plugin_redirect() {
    if (get_option('simple_plugin_do_activation_redirect', false)) {
        delete_option('simple_plugin_do_activation_redirect');
        wp_redirect('../wp-admin/options-general.php?page=htmlsitemap/admin.php');
    }
}