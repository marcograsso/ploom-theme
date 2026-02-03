<?php

declare(strict_types=1);

namespace App\WordPress;

use App\IsPluginActive;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class LoginPage
{
    public function __construct() {}

    // Update login page image link URL.
    #[Filter("login_headerurl")]
    public function set_logo_url(): string
    {
        return home_url();
    }

    // Update login page link title.
    #[Filter("login_headertext")]
    public function set_title(): string
    {
        return get_bloginfo("name");
    }

    #[Filter("login_display_language_dropdown")]
    public function disable_language_dropdown()
    {
        return false;
    }
}
