<?php

/**
 * Class PBPrivate
 * Function of the Plugin
 */
class PBPrivate {

    /**
     * @var bool if plugin is initiated
     */
    private static $initiated = false;

    /**
     * @var string Name of the settings array
     */
    protected static $option_name = 'pbprivate';

    /**
     * @var array Default values of the settings
     */
    protected static $data = array(
        'export' => false
    );

    /**
     *  Called by the init hook
     */
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

    /**
     * Handles the private Shortcode
     * @param $atts Attributes
     * @param string|null $content the content in between
     * @return string
     */
    public static function private_shortcode( $atts , $content = null ) {

        $options = get_option( 'pressbooks_theme_options_global' );

        //Return the content in the Shortcode if we are currently exporting and the export of the boxes is selected
        if((isset($_POST['export_formats']) || array_key_exists( 'format', $GLOBALS['wp_query']->query_vars )) && $options["private_boxes"]){
            return(do_shortcode($content));
        }else{
            return("");
        }
    }

    /**
     * Init Admin Hooks and add the setting
     */
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
                __( 'Export private sections (Never in webbook)', 'pbprivate' )
            )
        );

        add_filter( "sanitize_option_{$_option}", array( 'PBPrivate', 'sanitize' ), 11 );
    }

    /**
     * Output of the option
     * @param $args Arguments
     */
    public static function private_callback( $args ) {

        $options = get_option( 'pressbooks_theme_options_global' );

        if ( ! isset( $options['private_boxes'] ) ) {
            $options['private_boxes'] = 0;
        }

        $html = '<input type="checkbox" id="private_boxes" name="pressbooks_theme_options_global[private_boxes]" value="1" ' . checked( 1, $options['private_boxes'], false ) . '/>';
        $html .= '<label for="private_boxes"> ' . $args[0] . '</label>';
        echo $html;
    }

    /**
     * Callback if the option gets changed
     * @param $input
     * @return mixed
     */
    public static function sanitize($input){
        if ( ! isset( $_POST['pressbooks_theme_options_global']['private_boxes'] ) || $_POST['pressbooks_theme_options_global']['private_boxes'] != '1' ) {
            $input['private_boxes'] = 0;
        } else {
            $input['private_boxes'] = 1;
        }
        return($input);
    }


}