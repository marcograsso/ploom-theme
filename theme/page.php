<?php

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 */

use Timber\Timber;

$context = Timber::context();
$timber_post = Timber::get_post();
$context["post"] = $timber_post;

Timber::render(
    [
        "templates/page-" . $timber_post->ID . ".twig",
        "templates/page-" . $timber_post->slug . ".twig",
        "templates/page.twig",
    ],
    $context
);
