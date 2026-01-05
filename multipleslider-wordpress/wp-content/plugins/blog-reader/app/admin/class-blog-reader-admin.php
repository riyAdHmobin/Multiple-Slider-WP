<?php
/**
 * Class for admin methods.
 *
 * @package Blog_Reader
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'Blog_Reader_Admin' ) ) {

	/**
	 * Calls for admin methods.
	 */
	class Blog_Reader_Admin {

		/**
		 * Constructor for class.
		 */
		public function __construct() {
			// Enqueue custom scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'blp_br_enqueue_scripts' ) );
			// Custom AJAX.
			add_action( 'wp_ajax_blp_br_get_pages', array( $this, 'blp_br_get_pages_ajax_callback' ) );
		}

		/**
		 * Enqueue custom admin scripts.
		 */
		public function blp_br_enqueue_scripts() {
			$plugin_asset = 'blog-reader-admin';

			if ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) {
				// If Script debug disabled then include minified files.
				$plugin_asset .= '.min';
			}

			// Plugin Related Style and Scripts.
			wp_enqueue_script(
				'blog-reader-admin',
				trailingslashit( BLP_BR_URL ) . 'app/admin/assets/js/' . $plugin_asset . '.js',
				array( 'jquery', 'blp-br-select2' ),
				BLP_BR_VERSION,
				true
			);
			wp_enqueue_style(
				'blog-reader-admin',
				trailingslashit( BLP_BR_URL ) . 'app/admin/assets/css/' . $plugin_asset . '.css',
				array(),
				BLP_BR_VERSION
			);

			/**
			 * Credits: Select2( https://select2.org/ )
			 */
			wp_enqueue_style(
				'blp-br-select2',
				trailingslashit( BLP_BR_URL ) . 'assets/css/select2.min.css',
				array(),
				BLP_BR_VERSION
			);
			wp_enqueue_script(
				'blp-br-select2',
				trailingslashit( BLP_BR_URL ) . 'assets/js/select2.min.js',
				array( 'jquery' ),
				BLP_BR_VERSION,
				true
			);
		}

		/**
		 * Get pages ajax callback.
		 */
		public function blp_br_get_pages_ajax_callback() {
			$return = array();

			$search_key  = filter_input( INPUT_GET, 'search_key', FILTER_DEFAULT );
			$search_type = filter_input( INPUT_GET, 'search_type', FILTER_DEFAULT );

			switch ( $search_type ) {
				case 'memberpress':
					$allowed_post_types = apply_filters(
						'blpv_allowed_post_types_memberpress',
						array(
							'memberpressproduct',
						)
					);
					break;
				case 'learndash':
					$allowed_post_types = apply_filters(
						'blpv_allowed_post_types_learndash',
						array(
							'sfwd-courses',
						)
					);
					break;
				default:
					$allowed_post_types = apply_filters(
						'blp_br_allowed_post_types',
						array(
							'post',
							'page',
							'product',
						)
					);
					break;
			}

			$search_results = new WP_Query(
				array(
					's'                   => $search_key,
					'post_status'         => 'publish',
					'post_type'           => $allowed_post_types,
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 50,
				)
			);

			if ( $search_results->have_posts() ) :
				while ( $search_results->have_posts() ) :
					$search_results->the_post();
					// Shorten the title a little.
					$title    = ( mb_strlen( get_the_title() ) > 50 )
						? mb_substr( get_the_title(), 0, 49 ) . '...'
						: get_the_title();
					$return[] = array(
						get_the_ID(),
						$title,
					);
				endwhile;
			endif;
			echo wp_json_encode( $return );
			die;
		}
	}
	new Blog_Reader_Admin();
}
