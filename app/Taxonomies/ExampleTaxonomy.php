<?php

namespace App\Taxonomies;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\IconPicker;
use Extended\ACF\Location;

class ExampleTaxonomy
{
    // Tip: Use a singular name for your taxonomy name, such as location instead of locations.
    private static $names = [
        "singular" => "Example Taxonomy",
        "plural" => "Example Taxonomies",
        "slug" => "example-taxonomy",
    ];

    private static $post_types = ["post", "example"];

    public static function register()
    {
        self::register_taxonomy();
        self::register_custom_fields();
    }
    private static function register_taxonomy()
    {
        register_extended_taxonomy(
            self::$names["slug"],
            self::$post_types,
            [
                "hierarchical" => false,
                "exclusive" => false,
                "show_in_rest" => true,
            ],
            self::$names,
        );
    }

    private static function register_custom_fields()
    {
        register_extended_field_group([
            "title" => self::$names["singular"],
            "location" => [Location::where("taxonomy", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "",
            "fields" => [
                Text::make("Testo", "text"),
                IconPicker::make("Icon", "icon"),
            ],
        ]);
    }
}
