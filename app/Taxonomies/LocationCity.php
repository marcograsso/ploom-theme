<?php

namespace App\Taxonomies;

class LocationCity
{
    private static $names = [
        "singular" => "Città",
        "plural" => "Città",
        "slug" => "location-city",
    ];

    private static $post_types = ["locations"];

    public static function register()
    {
        register_extended_taxonomy(
            self::$names["slug"],
            self::$post_types,
            [
                "hierarchical" => true,
                "show_in_rest" => true,
            ],
            self::$names,
        );
    }
}
