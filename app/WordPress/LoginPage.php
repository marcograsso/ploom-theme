<?php

declare(strict_types=1);

namespace App\WordPress;

use Timber\Timber;
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

    #[Filter("login_head")]
    public function add_custom_logo(): void
    {
        $admin_logo_path =
            get_template_directory() . "/assets/images/admin-logo.svg";

        if (!file_exists($admin_logo_path)) {
            return;
        }

        $admin_logo_url =
            get_template_directory_uri() . "/assets/images/admin-logo.svg";
        ?>
            <style type="text/css">
                .login h1 a {
                    margin-bottom: 64px;
                    background-image: url('<?php echo esc_attr(
                        $admin_logo_url,
                    ); ?>');
                }

                p#nav, p#backtoblog {
                    text-align: center;
                }

                .login form {
                    border: none;
                    box-shadow: none;
                    border-radius: 1rem;
                }
            </style>
            <?php
    }

    #[Filter("login_display_language_dropdown")]
    public function disable_language_dropdown()
    {
        return false;
    }
}
