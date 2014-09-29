<?php
/**
 * @package PBPrivate
 */
/*
Plugin Name: PBPrivate
Plugin URI: https://github.com/lukaiser/PBPrivate
Description: By adding the shortcode [private]bla bla[/private] the content is not displayed in the web view of a PressBook. But you can decide to show it in the export.
Version: 0.9.0
Author: Lukas Kaiser
Author URI: http://emperor.ch
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'PBPrivate_VERSION', '0.9.0' );
define( 'PBPrivate__MINIMUM_WP_VERSION', '3.0' );
define( 'PBPrivate__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PBPrivate__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( PBPrivate__PLUGIN_DIR . 'class.pbprivate.php' );

add_action( 'init', array( 'PBPrivate', 'init' ) );