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
        Textarea::make("Frase ad effetto", "hero_claim")
            ->rows(3)
            ->newLines("br"),
    ],
    "style" => "",
    "hide_on_screen" => ["the_content"],
]);
