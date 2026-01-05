<?php

/**
 * Plugin Name:       Blog Reader
 * Plugin URI:        https://www.biliplugins.com
 * Description:       Simple text-to-speech Blog Reader.
 * Version:           1.3.1
 * Author:            BiliPlugins
 * Author URI:        https://www.biliplugins.com/blog-reader
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       blp-blog-reader
 * Domain Path:       /languages
 *
 * @package           Blog_Reader
 */
if ( !function_exists( 'blog_reader_fs' ) ) {
    /**
     * Create a helper function for easy SDK access.
     */
    function blog_reader_fs() {
        global $blog_reader_fs;
        if ( !isset( $blog_reader_fs ) ) {
            // Include Freemius SDK.
            require_once __DIR__ . '/vendor/autoload.php';
            $blog_reader_fs = fs_dynamic_init( array(
                'id'               => '13642',
                'slug'             => 'blog-reader',
                'type'             => 'plugin',
                'public_key'       => 'pk_ecc9d452fd61a85237989b70145ba',
                'is_premium'       => false,
                'premium_suffix'   => 'Pro',
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => false,
                'menu'             => array(
                    'slug'    => 'blp-blog-reader',
                    'support' => false,
                ),
                'is_live'          => true,
            ) );
        }
        return $blog_reader_fs;
    }

    // Init Freemius.
    blog_reader_fs();
    // Signal that SDK was initiated.
    do_action( 'blog_reader_fs_loaded' );
}
/**
 * Defining Constants.
 *
 * @package    Blog_Reader
 */
if ( !defined( 'BLP_BR_VERSION' ) ) {
    /**
     * The version of the plugin.
     */
    define( 'BLP_BR_VERSION', '1.3.1' );
}
if ( !defined( 'BLP_BR_PATH' ) ) {
    /**
     *  The server file system path to the plugin directory.
     */
    define( 'BLP_BR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'BLP_BR_URL' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'BLP_BR_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'BLP_BR_BASE_NAME' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'BLP_BR_BASE_NAME', plugin_basename( __FILE__ ) );
}
/**
 * Apply translation file as per WP language.
 */
function blp_br_text_domain_loader() {
    // Get mo file as per current locale.
    $mofile = BLP_BR_PATH . 'languages/' . get_locale() . '.mo';
    // If file does not exists, then apply default mo.
    if ( !file_exists( $mofile ) ) {
        $mofile = BLP_BR_PATH . 'languages/default.mo';
    }
    load_textdomain( 'blp-blog-reader', $mofile );
}

add_action( 'plugins_loaded', 'blp_br_text_domain_loader' );
/**
 * Setting link for plugin.
 *
 * @param  array $links Array of plugin setting link.
 * @return array
 */
function blp_br_setting_page_link(  $links  ) {
    $settings_link = sprintf( 
        '<a href="%1$s">%2$s</a>',
        esc_url( admin_url( 'admin.php?page=blp-blog-reader' ) ),
        // Change settings page slug.
        esc_html__( 'Settings', 'blp-blog-reader' )
     );
    array_unshift( $links, $settings_link );
    return $links;
}

add_filter( 'plugin_action_links_' . BLP_BR_BASE_NAME, 'blp_br_setting_page_link' );
// Include plugin related files here.
require BLP_BR_PATH . '/app/includes/common-functions.php';
require BLP_BR_PATH . '/app/admin/class-blog-reader-admin.php';
require BLP_BR_PATH . '/app/admin/class-blog-reader-settings.php';
require BLP_BR_PATH . '/app/public/class-blog-reader-public.php';