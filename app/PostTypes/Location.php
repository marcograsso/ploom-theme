<?php

namespace App\PostTypes;

use Extended\ACF\Fields\DatePicker;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use App\FieldTypes\Mapbox;

class Location extends \Timber\Post
{
    private static $names = [
        "singular" => "Location",
        "plural" => "Locations",
        "slug" => "locations",
    ];

    public static function register()
    {
        self::register_post_type();
        self::register_custom_fields();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });
    }

    private static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "menu_icon" => "dashicons-location",
            "menu_position" => null,
        ];

        register_extended_post_type($name, $args, $names);
    }

    private static function register_custom_fields()
    {
        $mapbox_api_key = get_field("mapbox_api_key", "option") ?? "";

        register_extended_field_group([
            "title" => self::$names["singular"],
            "location" => [
                \Extended\ACF\Location::where(
                    "post_type",
                    self::$names["slug"],
                ),
            ],
            "hide_on_screen" => ["the_content"],
            "style" => "",
            "fields" => [
                Tab::make("Posizione"),
                Mapbox::make("Map", "map")
                    ->mapbox_api_key($mapbox_api_key)
                    ->default_country("it"),
                Tab::make("Informazioni"),
                Select::make("Flavour", "flavour")
                    ->choices([
                        "arctic" => "Arctic Mint",
                        "wild" => "Wild Berry",
                        "shop" => "Shop",
                    ])
                    ->helperText("Seleziona il flavour associato a questa location."),
                Text::make("Indirizzo", "address"),
                Text::make("Periodo", "period"),
                DatePicker::make("Data fine", "end_date"),
                Text::make("Orari", "hours"),
            ],
        ]);
    }

    public function to_alpine(): array
    {
        $map = get_field("map", $this->ID) ?? [];
        $lat = !empty($map["latitude"]) ? (float) $map["latitude"] : null;
        $lng = !empty($map["longitude"]) ? (float) $map["longitude"] : null;

        return [
            "id" => $this->ID,
            "title" => $this->title,
            "flavour" => get_field("flavour", $this->ID) ?? "",
            "tags" => array_values(
                array_map(
                    fn($t) => $t->name,
                    get_the_terms($this->ID, "location-tag") ?: [],
                ),
            ),
            "address" => get_field("address", $this->ID) ?? "",
            "period" => get_field("period", $this->ID) ?? "",
            "end_date" => ($d = get_field("end_date", $this->ID))
                ? (
                    \DateTime::createFromFormat("d/m/Y", $d)
                        ?: \DateTime::createFromFormat("Ymd", $d)
                        ?: \DateTime::createFromFormat("Y-m-d", $d)
                )?->format("Y-m-d") ?? ""
                : "",
            "hours" => get_field("hours", $this->ID) ?? "",
            "city" => ($city_terms = get_the_terms($this->ID, "location-city")) && !is_wp_error($city_terms)
                ? $city_terms[0]->slug
                : "",
            "location" => [
                "latitude" => $lat,
                "longitude" => $lng,
            ],
        ];
    }
}
