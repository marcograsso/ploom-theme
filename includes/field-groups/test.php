<?php

use Extended\ACF\Fields\Text;
use Extended\ACF\Location;
use Localghost\Acf\Fields\Mapbox;
use Localghost\Acf\Fields\Money;

register_extended_field_group([
    "title" => "Test",
    "location" => [Location::where("post_type", "=", "post")],
    "fields" => [Money::make("Amount", "amount")],
    "style" => "",
    "hide_on_screen" => ["the_content"],
]);
