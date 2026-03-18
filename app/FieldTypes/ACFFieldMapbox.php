<?php

namespace App\FieldTypes;

use Timber\Timber;

if (!defined("ABSPATH")) {
    exit();
}

class ACFFieldMapbox extends \acf_field
{
    /**
     * Controls field type visibilty in REST requests.
     *
     * @var bool
     */
    public $show_in_rest = true;

    /**
     * Environment values relating to the theme or plugin.
     *
     * @var array $env Plugin or theme context such as 'url' and 'version'.
     */
    private $env;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /**
         * Field type reference used in PHP and JS code.
         *
         * No spaces. Underscores allowed.
         */
        $this->name = "mapbox";

        /**
         * Field type label.
         *
         * For public-facing UI. May contain spaces.
         */
        $this->label = __("Mapbox Address", "ploom");

        /**
         * The category the field appears within in the field type picker.
         */
        $this->category = "content";

        /**
         * Field type Description.
         *
         * For field descriptions. May contain spaces.
         */
        $this->description = __(
            "Address input with Mapbox geocoding and map preview",
            "ploom",
        );

        /**
         * Field type Doc URL.
         *
         * For linking to a documentation page. Displayed in the field picker modal.
         */
        $this->doc_url = "https://docs.mapbox.com/";

        /**
         * Field type Tutorial URL.
         *
         * For linking to a tutorial resource. Displayed in the field picker modal.
         */
        $this->tutorial_url = "";

        /**
         * Defaults for your custom user-facing settings for this field type.
         */
        $this->defaults = [
            "mapbox_api_key" => "",
            "default_country" => "us",
        ];

        /**
         * Strings used in JavaScript code.
         *
         * Allows JS strings to be translated in PHP and loaded in JS via:
         *
         * ```js
         * const errorMessage = acf._e("mapbox", "error");
         * ```
         */
        $this->l10n = [
            "no_api_key" => __(
                "Mapbox API key is required. Please add it in field settings.",
                "ploom",
            ),
            "enter_address" => __("Inserisci un indirizzo…", "ploom"),
        ];

        $this->env = [
            "url" => site_url(str_replace(ABSPATH, "", __DIR__)), // URL to the acf-mapbox directory.
            "version" => "1.0",
        ];

        parent::__construct();
    }

    /**
     * Settings to display when users configure a field of this type.
     *
     * These settings appear on the ACF "Edit Field Group" admin page when
     * setting up the field.
     *
     * @param array $field
     * @return void
     */
    public function render_field_settings($field)
    {
        acf_render_field_setting($field, [
            "label" => __("Mapbox API Key", "ploom"),
            "instructions" => __(
                "Enter your Mapbox public access token. Get one at https://account.mapbox.com/",
                "ploom",
            ),
            "type" => "text",
            "name" => "mapbox_api_key",
            "required" => true,
        ]);

        acf_render_field_setting($field, [
            "label" => __("Default Country", "ploom"),
            "instructions" => __(
                "Limit address search to specific country (e.g., us, gb, ca)",
                "ploom",
            ),
            "type" => "text",
            "name" => "default_country",
            "placeholder" => "us",
        ]);
    }

    /**
     * HTML content to show when a publisher edits the field on the edit screen.
     *
     * @param array $field The field settings and values.
     * @return void
     */
    public function render_field($field)
    {
        // Parse the value
        $value = wp_parse_args($field["value"], [
            "address" => "",
            "latitude" => "",
            "longitude" => "",
            "city" => "",
            "province" => "",
            "cap" => "",
        ]);

        $mapbox_api_key = isset($field["mapbox_api_key"])
            ? $field["mapbox_api_key"]
            : "";
        $default_country = isset($field["default_country"])
            ? $field["default_country"]
            : "us";

        echo Timber::compile("acf-mapbox.twig", [
            "mapbox_api_key" => $mapbox_api_key,
            "default_country" => $default_country,
            "field" => $field,
            "value" => $value,
            "l10n" => $this->l10n,
        ]);
    }

    /**
     * Enqueues CSS and JavaScript needed by HTML in the render_field() method.
     *
     * Callback for admin_enqueue_script.
     *
     * @return void
     */
    public function input_admin_enqueue_scripts()
    {
        $url = trailingslashit($this->env["url"]);
        $version = $this->env["version"];

        // Enqueue Mapbox Search JS library
        wp_enqueue_script(
            "mapbox-search-js",
            "https://api.mapbox.com/search-js/v1.5.0/web.js",
            [],
            "1.5.0",
            false,
        );
    }

    /**
     * Validates field value before saving.
     *
     * @param bool  $valid Whether the value is valid.
     * @param mixed $value The field value.
     * @param array $field The field array.
     * @param string $input The input name.
     * @return bool|string
     */
    public function validate_value($valid, $value, $field, $input)
    {
        // No validation needed - allow empty values
        return $valid;
    }

    /**
     * Formats the field value for the API.
     *
     * @param mixed $value The field value.
     * @param int   $post_id The post ID.
     * @param array $field The field array.
     * @return array
     */
    public function format_value($value, $post_id, $field)
    {
        // Ensure value is an array
        if (empty($value)) {
            return [
                "address" => "",
                "latitude" => "",
                "longitude" => "",
                "city" => "",
                "province" => "",
                "cap" => "",
            ];
        }

        return wp_parse_args($value, [
            "address" => "",
            "latitude" => "",
            "longitude" => "",
            "city" => "",
            "province" => "",
            "cap" => "",
        ]);
    }
}
