<?php

namespace App;

use App\Website;
use App\ACF;

use Timber\Timber;

require_once __DIR__ . "/vendor/autoload.php";

add_action("acf/include_fields", function () {
    foreach (glob(__DIR__ . "/includes/field-groups/*.php") as $file) {
        require_once $file;
    }
});

Timber::init();

new Website();

require_once __DIR__ . "/includes/hooks.php";
require_once __DIR__ . "/includes/routes.php";

new ACF();
