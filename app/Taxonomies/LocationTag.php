<?php

namespace App\Taxonomies;

class LocationTag
{
    private static $names = [
        "singular" => "Tag",
        "plural" => "Tag",
        "slug" => "location-tag",
    ];

    private static $post_types = ["locations"];

    public static function register()
    {
        register_extended_taxonomy(
            self::$names["slug"],
            self::$post_types,
            [
                "hierarchical" => false,
                "show_in_rest" => true,
            ],
            self::$names,
        );
    }
}
