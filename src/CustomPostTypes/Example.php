<?php

namespace App\CustomPostTypes;

use Extended\ACF\Fields\Text;

use Extended\ACF\Location;

class Example extends \Timber\Post
{
    // Tip: Use a singular name for your post type name, such as article instead of articles.
    private static $names = [
        "singular" => "Example",
        "plural" => "Examples",
        "slug" => "example",
    ];

    public static function register()
    {
        self::register_post_type();
        self::register_custom_fields();

        // Add the custom class to the Timber post classmap
        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });
    }
    public static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "menu_icon" => "dashicons-location",
            "menu_position" => null,
        ];

        register_extended_post_type($name, $args, $names);
    }

    public static function register_custom_fields()
    {
        register_extended_field_group([
            "title" => self::$names["singular"],
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "",
            "fields" => [Text::make("Testo", "text")],
        ]);
    }
}
