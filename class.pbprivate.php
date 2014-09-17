<?php

class PBPrivate {

    private static $initiated = false;

    // Name of the array
    protected static $option_name = 'pbprivate';

    // Default values
    protected static $data = array(
        'export' => false
    );

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks() {
        self::$initiated = true;

        add_shortcode( 'private', array( 'PBPrivate', 'private_shortcode' ) );
        add_action('admin_init', array('PBPrivate', 'admin_init'));

    }

    public static function private_shortcode( $atts , $content = null ) {

        $options = get_option( 'pressbooks_theme_options_global' );

        if(isset($_POST['export_formats']) && $options["private_boxes"]){
            return(do_shortcode($content));
        }else{
            return("");
        }
    }

    public static function admin_init() {
        register_setting('private_options', static::$option_name, array('PBPrivate', 'validate'));

        $_page = $_option = 'pressbooks_theme_options_global';
        $_section = 'global_options_section';

        add_settings_field(
            'private_boxes',
            __( 'Private', 'pbprivate' ),
            'PBPrivate::private_callback',
            $_page,
            $_section,
            array(
                __( 'Export private parts (Never in webbook)', 'pbprivate' )
            )
        );

        add_filter( "sanitize_option_{$_option}", array( 'PBPrivate', 'sanitize' ), 11 );
    }

    // Global Options Field Callback
    public static function private_callback( $args ) {

        $options = get_option( 'pressbooks_theme_options_global' );

        if ( ! isset( $options['private_boxes'] ) ) {
            $options['private_boxes'] = 0;
        }

        $html = '<input type="checkbox" id="private_boxes" name="pressbooks_theme_options_global[private_boxes]" value="1" ' . checked( 1, $options['private_boxes'], false ) . '/>';
        $html .= '<label for="private_boxes"> ' . $args[0] . '</label>';
        echo $html;
    }

    public static function sanitize($input){
        if ( ! isset( $_POST['pressbooks_theme_options_global']['private_boxes'] ) || $_POST['pressbooks_theme_options_global']['private_boxes'] != '1' ) {
            $input['private_boxes'] = 0;
        } else {
            $input['private_boxes'] = 1;
        }
        return($input);
    }


}