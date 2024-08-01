<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @link https://github.com/timber/starter-theme
 */

// Load Composer dependencies.

if (file_exists(__DIR__ . "/vendor")) {
    $vendor_path = __DIR__;
} else {
    $vendor_path = dirname(__DIR__);
}

require_once $vendor_path . "/vendor/autoload.php";

use Timber\Timber;

require_once __DIR__ . "/src/StarterSite.php";
require_once __DIR__ . "/src/hooks.php";
require_once __DIR__ . "/src/routes.php";

Timber::init();

new StarterSite();
