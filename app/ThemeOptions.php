<?php

namespace App;

use Extended\ACF\Fields\IconPicker;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\URL;
use Extended\ACF\Location;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Password;
use Extended\ACF\Fields\TrueFalse;

use Yard\Hook\Action;

class ThemeOptions
{
    public function __construct()
    {
        $this->register_options_page();
        $this->register_fields();
    }

    private function register_options_page()
    {
        acf_add_options_page([
            "icon_url" =>
                "data:image/svg+xml;base64," .
                base64_encode(
                    file_get_contents(
                        get_template_directory() .
                            "/assets/images/admin-icon.svg",
                    ),
                ),
            "menu_slug" => "theme-options",
            "page_title" => get_bloginfo("name"),
            "position" => 2.1,
        ]);
    }

    private function register_fields()
    {
        register_extended_field_group([
            "title" => "Globals",
            "fields" => [
                TrueFalse::make(
                    "Enable \"Coming Soon\" mode",
                    "enable_coming_soon",
                )
                    ->default(false)
                    ->helperText(
                        "Enable this to show a \"Coming Soon\" mode on the website to everyone except for logged in admins.",
                    ),
                Password::make("Mapbox API Key", "mapbox_api_key")->helperText(
                    "Inserisci la chiave API di Mapbox per il tuo sito.",
                ),
                URL::make("Privacy Policy URL", "privacy_policy_url"),
                URL::make("Cookie Policy URL", "cookie_policy_url"),
                Repeater::make("Social Links", "social_links")
                    ->helperText("Aggiungi i link ai profili social.")
                    ->layout("row")
                    ->fields([
                        IconPicker::make("Icon")
                            ->helperText("Aggiungi icona.")
                            ->format("string"),
                        URL::make("URL", "url")->helperText(
                            "Inserisci il link al profilo social.",
                        ),
                    ]),
            ],
            "style" => "",
            "location" => [Location::where("options_page", "theme-options")],
        ]);
    }
}
