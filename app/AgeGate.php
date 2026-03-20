<?php

namespace App;

use Yard\Hook\Action;

class AgeGate
{
    private const COOKIE_NAME = "ploom_age_verified";
    private const COOKIE_DURATION = 30 * DAY_IN_SECONDS;

    #[Action("template_redirect")]
    public function handle(): void
    {
        // Set cookie and redirect to home when user confirms age
        if (isset($_GET["age_verified"])) {
            setcookie(
                self::COOKIE_NAME,
                "1",
                time() + self::COOKIE_DURATION,
                "/",
                "",
                is_ssl(),
                true,
            );
            wp_safe_redirect(home_url("/"));
            exit();
        }

        // Skip redirect for admin and REST API
        if (is_admin() || (defined("REST_REQUEST") && REST_REQUEST)) {
            return;
        }

        // Skip redirect if already on age-gate page
        if ($this->is_age_gate_page()) {
            return;
        }

        // Redirect to age-gate if cookie not set
        if (empty($_COOKIE[self::COOKIE_NAME])) {
            wp_safe_redirect(home_url("/" . $this->get_age_gate_slug() . "/"));
            exit();
        }
    }

    private function is_age_gate_page(): bool
    {
        $queried = get_queried_object();

        return $queried instanceof \WP_Post &&
            $queried->post_name === $this->get_age_gate_slug();
    }

    private function get_age_gate_slug(): string
    {
        $pages = get_pages([
            "meta_key" => "_wp_page_template",
            "meta_value" => "views/templates/page-age-gate.twig",
            "number" => 1,
        ]);

        if (!empty($pages)) {
            return $pages[0]->post_name;
        }

        return "age-gate";
    }
}
