<?php

declare(strict_types=1);

namespace App\WordPress;

use App\IsPluginActive;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class AdminBar
{
    public function __construct() {}

    #[Action("wp_before_admin_bar_render", 9999)]
    public function remove_items(): void
    {
        global $wp_admin_bar;

        $disposable_nodes = ["comments", "themes", "updates", "wp-logo"];

        foreach ($disposable_nodes as $node) {
            $wp_admin_bar->remove_menu($node);
        }
    }
}
