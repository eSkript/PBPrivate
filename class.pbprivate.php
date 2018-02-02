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
        add_filter( 'query_vars', array('PBPrivate', 'add_query_vars_filter') );
        add_filter( 'parse_query', array('PBPrivate', 'change_show_setting') );
        add_filter( 'wp_enqueue_scripts', array('PBPrivate', 'add_button_to_chapters') );
        add_filter( 'wp_enqueue_scripts', array('PBPrivate', 'add_css') );
        //Add Style to Export
        add_filter( 'pb_epub_css_override', array( 'PBPrivate', 'scssOverrides' ) );
        add_filter( 'pb_pdf_css_override', array( 'PBPrivate', 'scssOverrides' ) );
        add_filter( 'pb_mpdf_css_override', array( 'PBPrivate', 'scssOverrides' ) );
        add_filter( 'pb_web_css_override', array( 'PBPrivate', 'scssOverrides' ) );
    }

    /**
     * Handles the private Shortcode [private][/private]
     * @param $atts Attributes
     * @param string|null $content the content in between
     * @return string
     */
    public static function private_shortcode( $atts , $content = null ) {
        global $post;
        $options = get_option( 'pressbooks_theme_options_global' );

        //Return the content in the Shortcode if we are currently exporting and the export of the boxes is selected
        if((isset($_POST['export_formats']) || array_key_exists( 'format', $GLOBALS['wp_query']->query_vars )) && $options["private_boxes"]){
            return('<table class="PBPrivate"><tr><td class="PBPrivate-header"><img src="'.PBPrivate__PLUGIN_URL.'export/img/lock.svg"/><span class="hidden"><h2>Private Content</h2></span></td><td class="PBPrivate-content">'.do_shortcode($content).'</td></tr></table>');
        //Return the content if the user can edit the post and has selected to show the content on the webpage
        }else if(current_user_can('edit_post', $post->ID) && get_user_meta( get_current_user_id(), "PBShowPrivate", true )){
            return('<div class="PBPrivate"><h2 class="dashicons dashicons-lock PBPrivate-header"><span class="hidden">Private Content</span></h2>'.do_shortcode($content).'</div>'); 
        }else{
            return("");
        }
    }

    /**
     * Init Admin Hooks and add the setting
     */
    public static function admin_init() {
        //Add the admin setting that alows to select if the private sections are exported
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
                __( 'Export private sections', 'pbprivate' )
            )
        );

        add_filter( "sanitize_option_{$_option}", array( 'PBPrivate', 'sanitize' ), 11 );

        //Adds the button to the editor
        add_filter( 'mce_external_plugins', function ( $plugin_array ) {
            $plugin_array['pbprivate'] = PBPrivate__PLUGIN_URL .'admin/js/mcebutton.js';
            return $plugin_array;
        });
        add_filter( 'mce_buttons_2', function( $buttons ) {
            array_push( $buttons, 'pbprivate' );
            return $buttons;
        });
    }

    /**
     * Output of the admin option
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
     * Callback if the admin option gets changed
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
    
    /**
     * Add query vars, enabling the button on the page
     * @param $vars
     * @return array
     */
    public static function add_query_vars_filter( $vars ){
        $vars[] = "show_private";
        $vars[] = "hide_private";
        return $vars;
    }

    /**
     * Changing the user setting if the private sections are shown on the webpage
     */
    public static function change_show_setting(){ 
        if(is_user_logged_in()){
            if(get_query_var('show_private', false )){
                update_user_meta(get_current_user_id(), "PBShowPrivate", true);
            }
            if(get_query_var('hide_private', false )){
                update_user_meta(get_current_user_id(), "PBShowPrivate", false);
            }
        }
    }

    /**
     * Adds the button to the webpage allowing the user to change the display setting
     */
    public static function add_button_to_chapters(){
        global $post;
        if(is_single() && current_user_can('edit_post', $post->ID)){
            wp_register_script( 'PBPrivate', PBPrivate__PLUGIN_URL .'public/js/button.js' );
            $value_array = array(
                //'some_string' => __( 'Some string to translate', 'plugin-domain' ),
                'is_on' => get_user_meta( get_current_user_id(), "PBShowPrivate", true ),
                'hide_url' => add_query_arg( array(
                    'hide_private' => true,
                    'show_private' => false,
                ), $_SERVER['REQUEST_URI'] ),
                'show_url' => add_query_arg( array(
                    'hide_private' => false,
                    'show_private' => true,
                ), $_SERVER['REQUEST_URI'] )
            );
            wp_localize_script( 'PBPrivate', 'PBPrivate', $value_array );
            wp_enqueue_script( 'PBPrivate' );
        }
    }

    /**
     * Adds custom css to the webpage
     */
    public static function add_css(){
        wp_enqueue_style( 'PBPrivate', PBPrivate__PLUGIN_URL.'public/css.css', array(), PBPrivate_VERSION);
    }

    public static function scssOverrides($css){
        $css .= file_get_contents(PBPrivate__PLUGIN_DIR.'export/css.css');
        return $css;
    }

}