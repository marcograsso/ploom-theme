<?php

namespace App;

require_once __DIR__ . "/vendor/autoload.php";

new Bootstrap();

add_action("admin_head", function () {
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    $screen->remove_help_tabs();
});
