<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;
use Yard\Hook\Filter;

#[IsPluginActive("advanced-custom-fields-pro/acf.php")]
class AdvancedCustomFields
{
    public function __construct()
    {
        $this->register_icons();
    }

    public function register_icons()
    {
        // Add the custom icons to the icon picker
        $icons_path = get_template_directory() . "/views/icons/";
        $icons = glob($icons_path . "*/");

        foreach ($icons as $icon) {
            $folder_name = basename($icon);
            $filter = "acf/fields/icon_picker/{$folder_name}/icons";
            add_filter($filter, function (array $icons) use (
                $folder_name,
            ): array {
                $base_url =
                    get_template_directory_uri() .
                    "/views/icons/{$folder_name}/";
                $icons_path =
                    get_template_directory() . "/views/icons/{$folder_name}/";
                $custom_icons = [];

                if (!is_dir($icons_path)) {
                    return $custom_icons;
                }

                $svg_files = glob($icons_path . "*.svg");

                foreach ($svg_files as $svg_file) {
                    $filename = basename($svg_file, ".svg");
                    $label = ucfirst($filename);

                    $custom_icons[] = [
                        "url" => $base_url . $filename . ".svg",
                        "key" => $filename,
                        "label" => $label,
                    ];
                }

                return $custom_icons;
            });
        }
    }

    #[Filter("timber/context")]
    public function add_options_to_context($context)
    {
        $context["options"] = get_fields("options");

        return $context;
    }

    #[Filter("acf_icon_path_suffix")]
    public function icon_path_suffix($path_suffix)
    {
        return "views/icons/";
    }

    #[Filter("acf/fields/icon_picker/tabs")]
    public function icon_picker_tabs($tabs)
    {
        $tabs = [];

        $icons_path = get_template_directory() . "/views/icons/";
        $icons = glob($icons_path . "*/");
        foreach ($icons as $icon) {
            $tabs[basename($icon)] = ucfirst(basename($icon));
        }

        return $tabs;
    }
}
