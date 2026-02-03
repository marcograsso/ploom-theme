<?php

declare(strict_types=1);

namespace App\WordPress;

use App\IsPluginActive;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class Dashboard
{
    #[Action("wp_dashboard_setup")]
    public function remove_widgets(): void
    {
        $widgets = [
            [
                "context" => "side",
                "priority" => "core",
                "id" => "dashboard_primary",
            ],
            [
                "context" => "side",
                "priority" => "core",
                "id" => "dashboard_quick_press",
            ],
            [
                "context" => "side",
                "priority" => "core",
                "id" => "jetpack_summary_widget",
            ],
        ];

        foreach ($widgets as $widget) {
            remove_meta_box($widget["id"], "dashboard", $widget["context"]);
        }

        // Remove welcome panel
        remove_action("welcome_panel", "wp_welcome_panel");
    }
}
