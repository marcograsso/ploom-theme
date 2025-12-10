<?php

namespace App;

class ACF
{
    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        $this->customize_icon_picker();
    }

    public function customize_icon_picker()
    {
        ray("Customizing icon picker");
        // Add the suffix to the icon path
        add_filter("acf_icon_path_suffix", function ($path_suffix) {
            return "views/icons/";
        });

        // Add the custom tab to the icon picker
        add_filter("acf/fields/icon_picker/tabs", function (
            array $tabs,
        ): array {
            $tabs = [];

            $icons_path = get_template_directory() . "/views/icons/";
            $icons = glob($icons_path . "*/");
            foreach ($icons as $icon) {
                $tabs[basename($icon)] = ucfirst(basename($icon));
            }

            return $tabs;
        });

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
}
