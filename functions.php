<?php

use Timber\Timber;

// Load Composer dependencies.
if (file_exists(__DIR__ . "/vendor")) {
    $vendor_path = __DIR__;
} else {
    $vendor_path = dirname(__DIR__);
}

require_once $vendor_path . "/vendor/autoload.php";

require_once __DIR__ . "/src/StarterSite.php";
require_once __DIR__ . "/src/hooks.php";
require_once __DIR__ . "/src/routes.php";

Timber::init();
new StarterSite();
