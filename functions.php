<?php

namespace App;

use App\Website;
use Timber\Timber;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/includes/hooks.php";
require_once __DIR__ . "/includes/routes.php";

Timber::init();

new Website();
