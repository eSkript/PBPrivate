<?php
/**
 * 
 * @link    https://github.com/lukaiser
 * @since   1.0.0
 * @package PBPrivate
 *
 */
/*
 * @wordpress-plugin
 * Plugin Name: PBPrivate
 * Plugin URI: https://github.com/lukaiser/PBPrivate
 * Description: This plugin allows Pressbooks editors to insert private sections in to their chapters.
 * Version: 1.0.0
 * Author: Lukas Kaiser
 * Author URI: https://github.com/lukaiser
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'PBPrivate_VERSION', '1.0.0' );
define( 'PBPrivate__MINIMUM_WP_VERSION', '3.0' );
define( 'PBPrivate__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PBPrivate__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( PBPrivate__PLUGIN_DIR . 'class.pbprivate.php' );

add_action( 'init', array( 'PBPrivate', 'init' ) );