<?php

use Timber\Site;
use Timber\Timber;

/**
 * Class StarterSite
 */
class StarterSite extends Site
{
    public function __construct()
    {
        add_action("after_setup_theme", [$this, "theme_supports"]);
        add_action("wp_enqueue_scripts", [$this, "enqueue_assets"]);

        add_filter("timber/context", [$this, "add_to_context"]);
        add_filter("timber/twig", [$this, "add_to_twig"]);
        add_filter("timber/twig/environment/options", [
            $this,
            "update_twig_environment_options",
        ]);

        parent::__construct();
    }

    public function enqueue_assets()
    {
        global $wp_styles;
        foreach ($wp_styles->queue as $key => $handle) {
            if (strpos($handle, "wp-block-") === 0) {
                wp_dequeue_style($handle);
            }
        }
        wp_dequeue_style("global-styles");
        wp_dequeue_script("jquery");

        $vite_env = "production";

        if (file_exists(get_template_directory() . "/../config.json")) {
            $config = json_decode(
                file_get_contents(get_template_directory() . "/../config.json"),
                true
            );
            $vite_env = $config["vite"]["environment"] ?? "production";
        }

        $dist_uri = get_template_directory_uri() . "/assets/dist";
        $dist_path = get_template_directory() . "/assets/dist";
        $manifest = null;

        if (file_exists($dist_path . "/.vite/manifest.json")) {
            $manifest = json_decode(
                file_get_contents($dist_path . "/.vite/manifest.json"),
                true
            );
        }

        if (is_array($manifest)) {
            if ($vite_env === "production" || is_admin()) {
                $js_file = "theme/assets/main.js";
                wp_enqueue_style(
                    "main",
                    $dist_uri . "/" . $manifest[$js_file]["css"][0]
                );
                wp_enqueue_script(
                    "main",
                    $dist_uri . "/" . $manifest[$js_file]["file"],
                    [],
                    "",
                    [
                        "strategy" => "defer",
                        "in_footer" => true,
                    ]
                );
            }
        }

        if ($vite_env === "development") {
            add_action("wp_head", function () {
                echo '<script type="module" crossorigin src="http://localhost:3000/@vite/client"></script>';
                echo '<script type="module" crossorigin src="http://localhost:3000/theme/assets/main.js"></script>';
            });
        }
    }

    /**
     * This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context($context)
    {
        $context["menu"] = Timber::get_menu();
        $context["site"] = $this;

        return $context;
    }

    public function theme_supports()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support("automatic-feed-links");

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support("title-tag");

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support("post-thumbnails");

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support("html5", [
            "comment-form",
            "comment-list",
            "gallery",
            "caption",
        ]);

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support("post-formats", [
            "aside",
            "image",
            "video",
            "quote",
            "link",
            "gallery",
            "audio",
        ]);

        add_theme_support("menus");
    }

    /**
     * This is where you can add your own functions to twig.
     *
     * @param Twig\Environment $twig get extension.
     */
    public function add_to_twig($twig)
    {
        return $twig;
    }

    /**
     * Updates Twig environment options.
     *
     * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
     *
     * @param array $options An array of environment options.
     *
     * @return array
     */
    function update_twig_environment_options($options)
    {
        // $options['autoescape'] = true;

        return $options;
    }
}
