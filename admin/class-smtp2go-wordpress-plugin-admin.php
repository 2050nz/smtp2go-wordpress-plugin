<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://thefold.nz
 * @since      1.0.0
 *
 * @package    Smtp2go_Wordpress_Plugin
 * @subpackage Smtp2go_Wordpress_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smtp2go_Wordpress_Plugin
 * @subpackage Smtp2go_Wordpress_Plugin/admin
 * @author     The Fold <hello@thefold.co.nz>
 */
class Smtp2goWordpressPluginAdmin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Save the options from the admin page
     * @since 1.0.0
     * @return void
     */
    // public function updateOptions()
    // {
    //     wp_redirect('/wp-admin/tools.php?page=' . $this->plugin_name);
    // }

    /**
     * Register all settings fields for the admin page
     *
     * @since 1.0.0
     * @return void
     */
    public function registerSettings()
    {
        /** add sections */
        add_settings_section(
            'smtp2go_settings_section',
            'General',
            array($this, 'generalSection'),
            $this->plugin_name
        );

        add_settings_section(
            'smtp2go_custom_headers_section',
            'Custom Headers',
            array($this, 'customHeadersSection'),
            $this->plugin_name
        );

        /** api key field */
        register_setting(
            'api_settings',
            'smtp2go_api_key',
            array($this, 'validateApiKey')
        );

        add_settings_field(
            'smtp2go_api_key',
            __('API Key *', $this->plugin_name),
            array($this, 'outputTextFieldHtml'),
            $this->plugin_name,
            'smtp2go_settings_section',
            array('name' => 'smtp2go_api_key', 'required' => true)
        );

        /** from email address field */
        register_setting(
            'api_settings',
            'smtp2go_from_address'
        );

        add_settings_field(
            'smtp2go_from_address',
            __('From Email Address *', $this->plugin_name),
            [$this, 'outputTextFieldHtml'],
            $this->plugin_name,
            'smtp2go_settings_section',
            array('name' => 'smtp2go_from_address', 'type' => 'email', 'required' => true)
        );

        /** from name field */
        register_setting(
            'api_settings',
            'smtp2go_from_name'
        );

        add_settings_field(
            'smtp2go_from_name',
            __('From Email Name *', $this->plugin_name),
            [$this, 'outputTextFieldHtml'],
            $this->plugin_name,
            'smtp2go_settings_section',
            array('name' => 'smtp2go_from_name', 'required' => true)
        );

        /**custom headers in own section */
        register_setting(
            'api_settings',
            'smtp2go_custom_headers'
        );

        add_settings_field(
            'smtp2go_custom_headers',
            false,
            [$this, 'outputCustomHeadersHtml'],
            $this->plugin_name,
            'smtp2go_custom_headers_section',
            array('class' => 'smtp2go_hide_title')
        );

        add_filter('pre_update_option_smtp2go_custom_headers', array($this, 'cleanCustomHeaderOptions'));
    }

    /**
     * Clean empty values out of the custom header options $_POST
     *
     * @since 1.0.0
     * @param array $options
     * @return array
     */
    public function cleanCustomHeaderOptions($options)
    {
        $final = array('header' => array(), 'value' => array());

        if (!empty($options['header'])) {
            foreach ($options['header'] as $index => $value) {
                if (!empty($value) && !empty($options['value'][$index])) {
                    $final['header'][] = $value;
                    $final['value'][]  = $options['value'][$index];
                }
            }
        }

        return $final;

    }

    /**
     * Output the html for managing custom headers
     *
     * @since 1.0.0
     * @return void
     */
    public function outputCustomHeadersHtml()
    {
        $existing_fields = '';

        $custom_headers = get_option('smtp2go_custom_headers');

        if (!empty($custom_headers['header'])) {
            foreach ($custom_headers['header'] as $index => $existing_custom_header) {
                $existing_fields .=
                    '<tr>'
                    . '<td><input  class="smtp2go_text_input" type="text" name="smtp2go_custom_headers[header][]" value="' . $existing_custom_header . '"/></td>'
                    . '<td><input  class="smtp2go_text_input" type="text" name="smtp2go_custom_headers[value][]" value="' . $custom_headers['value'][$index] . '"/></td>'
                    . '</tr>';
            }
        }

        echo '<table class="smtp2go_custom_headers">'
        . '<tr>'
        . '<th class="heading">' . __('Header', $this->plugin_name) . '</th>'
        . '<th class="heading">' . __('Value', $this->plugin_name) . '</th>'
        . $existing_fields
        . '<tr>'
        . '<td><input class="smtp2go_text_input" type="text" placeholder="' . __('Enter New Header Key', $this->plugin_name) . '" name="smtp2go_custom_headers[header][]"/></td>'
        . '<td><input  class="smtp2go_text_input" type="text" placeholder="' . __('Enter New Header Value', $this->plugin_name) . '" name="smtp2go_custom_headers[value][]"/></td>'
            . '</tr>';
    }

    public function generalSection()
    {
        return;
    }

    public function customHeadersSection()
    {
        echo '<small class="smtp2go_help_text">'
        . __('To remove a header, simply clear one of the values and save', SMTP_TEXT_DOMAIN)
            . '</small>';
    }

    /**
     * Output Text Field Html
     *
     * @param array $args
     * @return void
     */
    public function outputTextFieldHtml($args)
    {
        $field_name = $args['name'];

        $setting = get_option($field_name);

        if (empty($setting)) {
            $setting = '';
        }
        $required = '';
        if (!empty($args['required'])) {
            $required = 'required="required"';
        }

        $type = 'text';
        if (!empty($args['type'])) {
            $type = $args['type'];
        }
        echo '<input type="' . $type . '"' . $required . ' class="smtp2go_text_input" name="' . $field_name . '" value="' . esc_attr($setting) . '"/> ';
    }

    /**
     * Add Menu Page
     *
     * @return void
     */
    public function addMenuPage()
    {
        add_menu_page(
            'SMTP2Go',
            'SMTP2Go',
            'manage_options',
            $this->plugin_name,
            [$this, 'renderManagementPage']
        );
    }

    public function renderManagementPage()
    {
        //fetch all the options

        //display the page
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/smtp2go-wordpress-plugin-admin-display.php';
    }
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueueStyles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/smtp2go-wordpress-plugin-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueueScripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/smtp2go-wordpress-plugin-admin.js', array('jquery'), $this->version, false);
    }

    /** input validations */

    /**
     * Validate the api key
     *
     * @param string $input
     * @return string
     */
    public function validateApiKey($input)
    {
        if (empty($input) || strpos($input, 'api-') !== 0) {
            add_settings_error('smtp2go_messages', 'smtp2go_message', __('Invalid Api key entered.', SMTP_TEXT_DOMAIN));
            return get_option('smtp2go_api_key');
        }
        return sanitize_text_field($input);
    }
}
