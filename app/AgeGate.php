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

        // Skip if coming soon mode is active — it takes priority
        if (get_field("enable_coming_soon", "option")) {
            return;
        }

        // Cookie already set — no redirect needed
        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            return;
        }

        $age_gate_id = $this->get_age_gate_page_id();

        // No age-gate page found — bail to avoid redirect loops
        if (!$age_gate_id) {
            return;
        }

        // Already on age-gate page — do not redirect
        if (is_page($age_gate_id)) {
            return;
        }

        wp_safe_redirect(get_permalink($age_gate_id));
        exit();
    }

    private function get_age_gate_page_id(): ?int
    {
        $page = get_page_by_path("age-gate");

        return $page instanceof \WP_Post ? $page->ID : null;
    }
}
