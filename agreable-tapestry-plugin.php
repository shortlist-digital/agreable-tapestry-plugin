
<?php
/**
* @wordpress-plugin
* Plugin Name: Agreable Tapestry Plugin
* Plugin URI: http://github.com/shortlist-digital/agreable-tapestry-plugin
* Description: WordPress plugin to enable Tapesty - a React frontend for WordPress
* Version: 1.0.0
* Author: Shortlist Studio
* Author URI: http://shortlist.studio
* License: MIT
*/
class AgreableTapestryPlugin
{
		public function __construct()
		{
			add_action('wp', [$this, 'on_wp_action']);
		}

		public function on_wp_action()
		{
			global $post;

			if (!$post || !isset($post->post_status) || $post->post_status === 'publish') {
				return; // At the moment we are only interested in non-published posts
			}

			// To allow booting of Tapestry we must add the JSON to the document
			// for JavaScript to access
			$this->add_post_json_to_document($post);
		}

		public function add_post_json_to_document($post)
		{
			$request = new WP_REST_Request('GET', "/wp/v2/posts/" . $post->ID);
			$response = rest_get_server()->dispatch($request);
			if ($response->status !== 200) {
				echo "Error retrieving Post from API (HTTP . {$response->status})";
				exit;
			}

			// TODO: Cleaner implementation
			echo "<!doctype html><head>";

			wp_head();

			echo "</head><body>";

			echo "<script>window.tapestryPost = " . json_encode($response->data) . "</script>";

			$this->boot_tapestry();

			wp_footer();

			echo "</body>";

			exit; // TODO: Is there a better way of forcing the Theme not to load?
		}

		public function boot_tapestry()
		{
			echo "Tapestry loading&hellip;";

			//TODO Boot up the Tapestry client
		}
}
new AgreableTapestryPlugin();
