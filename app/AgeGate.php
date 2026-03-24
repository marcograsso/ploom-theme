<?php

declare(strict_types=1);

namespace App;

use Yard\Hook\Action;

class AgeGate
{
    private const COOKIE_NAME = "ploom_age_verified";
    private const COOKIE_DURATION = 30 * DAY_IN_SECONDS;
    private const PAGE_SLUG = "age-gate";

    #[Action("template_redirect")]
    public function handle(): void
    {
        if ($this->should_skip()) {
            return;
        }

        if (isset($_GET["age_verified"])) {
            $this->set_cookie();
            $this->redirect(home_url("/"));
        }

        $has_cookie = !empty($_COOKIE[self::COOKIE_NAME]);
        $is_age_gate = is_page(self::PAGE_SLUG);

        if ($has_cookie && $is_age_gate) {
            $this->redirect(home_url("/"));
        }

        if (!$has_cookie && !$is_age_gate) {
            $this->redirect(home_url("/" . self::PAGE_SLUG . "/"));
        }
    }

    private function should_skip(): bool
    {
        return is_admin() ||
            wp_doing_ajax() ||
            wp_doing_cron() ||
            (defined("REST_REQUEST") && REST_REQUEST) ||
            (defined("WP_CLI") && WP_CLI);
    }

    private function redirect(string $url): never
    {
        nocache_headers();
        wp_safe_redirect($url);
        exit();
    }

    private function set_cookie(): void
    {
        setcookie(self::COOKIE_NAME, "1", [
            "expires" => time() + self::COOKIE_DURATION,
            "path" => "/",
            "secure" => is_ssl(),
            "httponly" => true,
            "samesite" => "Lax",
        ]);
    }
}
