<?php

namespace App\Fields\Groups;

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Image;
use Extended\ACF\Location;
use Extended\ACF\Fields\WYSIWYGEditor;

register_extended_field_group([
    "title" => "Homepage",
    "location" => [Location::where("page_type", "=", "front_page")],
    "fields" => [
        Tab::make("Hero"),
        Group::make("", "hero_group")
            ->fields(
                require get_stylesheet_directory() .
                    "/views/components/hero/hero.php",
            )
            ->withSettings([
                "acfe_seamless_style" => 1,
                "acfe_group_modal" => 0,
                "acfe_group_modal_close" => 0,
                "acfe_group_modal_button" => "",
                "acfe_group_modal_size" => "large",
            ]),
        Tab::make("Flavors"),
        Group::make("", "flavors_group")
            ->fields(
                require get_stylesheet_directory() .
                    "/views/components/flavors/flavors.php",
            )
            ->withSettings([
                "acfe_seamless_style" => 1,
                "acfe_group_modal" => 0,
                "acfe_group_modal_close" => 0,
                "acfe_group_modal_button" => "",
                "acfe_group_modal_size" => "large",
            ]),
        Tab::make("Experience"),
        Group::make("", "experience_group")
            ->fields(
                require get_stylesheet_directory() .
                    "/views/components/experience/experience.php",
            )
            ->withSettings([
                "acfe_seamless_style" => 1,
                "acfe_group_modal" => 0,
                "acfe_group_modal_close" => 0,
                "acfe_group_modal_button" => "",
                "acfe_group_modal_size" => "large",
            ]),
    ],
    "style" => "",
    "hide_on_screen" => ["the_content"],
]);
