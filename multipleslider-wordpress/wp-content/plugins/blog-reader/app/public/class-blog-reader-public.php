<?php

/**
 * Class for public methods.
 *
 * @package Blog_Reader
 */
/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// If class is exist, then don't execute this.
if ( !class_exists( 'Blog_Reader_Public' ) ) {
    /**
     * Calls for public methods.
     */
    class Blog_Reader_Public {
        /**
         * Constructor for class.
         */
        public function __construct() {
            // Enqueue custom scripts.
            add_action( 'wp_enqueue_scripts', array($this, 'blp_br_enqueue_scripts') );
            add_filter( 'the_content', array($this, 'blp_br_add_button') );
        }

        /**
         * Add Blog Reader section.
         *
         * @param string $content Post Content.
         */
        public function blp_br_add_button( $content ) {
            $blp_br_options = get_option( 'blp_blog_options' );
            $blp_blog_reader_enabled = ( !empty( $blp_br_options['blp_blog_reader_enabled'] ) ? $blp_br_options['blp_blog_reader_enabled'] : 0 );
            if ( empty( $blp_blog_reader_enabled ) || empty( $content ) ) {
                return $content;
            }
            $allowed_post_types = array('post');
            $wrapper_classes = 'br-controls';
            $is_eligible = true;
            if ( is_array( $allowed_post_types ) && !in_array( get_post_type(), $allowed_post_types, true ) ) {
                return $content;
            }
            if ( empty( $is_eligible ) ) {
                return $content;
            }
            if ( is_singular() ) {
                ob_start();
                do_action( 'blp_br_before_controls' );
                ?>
				<div class="<?php 
                echo esc_attr( $wrapper_classes );
                ?>">
					<button id="br-tts-play" class="br-button" aria-label="<?php 
                esc_html_e( 'Play audio', 'blp-blog-reader' );
                ?>"><span class="gg-play-button-o"></span></button>
					<button id="br-tts-stop" class="br-button" aria-label="<?php 
                esc_html_e( 'Stop audio', 'blp-blog-reader' );
                ?>"><span class="gg-play-stop-o"></span></button>
				</div>
				<?php 
                do_action( 'blp_br_after_controls' );
                $br_controls = ob_get_clean();
                $content = $br_controls . '<div class="br-post-content">' . $content . '</div>';
            }
            return $content;
        }

        /**
         * Enqueue custom frontend scripts.
         */
        public function blp_br_enqueue_scripts() {
            $blp_br_options = get_option( 'blp_blog_options' );
            $blp_blog_reader_enabled = ( !empty( $blp_br_options['blp_blog_reader_enabled'] ) ? $blp_br_options['blp_blog_reader_enabled'] : 0 );
            if ( empty( $blp_blog_reader_enabled ) ) {
                return;
            }
            $plugin_asset = 'blog-reader-public';
            if ( !defined( 'SCRIPT_DEBUG' ) || !SCRIPT_DEBUG ) {
                // If Script debug disabled then include minified files.
                $plugin_asset .= '.min';
            }
            // Plugin Related Style and Scripts.
            wp_enqueue_script(
                'blog-reader-public',
                trailingslashit( BLP_BR_URL ) . 'app/public/assets/js/' . $plugin_asset . '.js',
                array('jquery'),
                BLP_BR_VERSION,
                true
            );
            wp_enqueue_style(
                'blog-reader-public',
                trailingslashit( BLP_BR_URL ) . 'app/public/assets/css/' . $plugin_asset . '.css',
                array(),
                BLP_BR_VERSION
            );
        }

    }

    new Blog_Reader_Public();
}