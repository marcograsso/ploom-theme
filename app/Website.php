<?php

namespace App;

use Timber\Site;
use Timber\Timber;
use Timber\URLHelper;

use App\Vite;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class Website extends Site
{
    public function __construct()
    {
        $this->vite = new Vite();
        parent::__construct();
    }

    #[Action("init")]
    public function register_post_types()
    {
        PostTypes\Example::register();
    }

    #[Action("init")]
    public function register_taxonomies()
    {
        Taxonomies\ExampleTaxonomy::register();
    }

    #[Action("wp_enqueue_scripts")]
    public function enqueue_frontend_assets()
    {
        $vite = $this->vite;

        if (is_array($vite->manifest)) {
            if ($vite->environment === "production" || is_admin()) {
                $js_file = "src/main.js";
                wp_enqueue_style(
                    "main",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["css"][0],
                );
                wp_enqueue_script(
                    "main",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["file"],
                    [],
                    "",
                    [
                        "strategy" => "defer",
                        "in_footer" => true,
                    ],
                );
            }
        }

        if ($vite->environment === "development") {
            add_action("wp_head", function () use ($vite) {
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    '@vite/client"></script>';
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    'src/main.js"></script>';
            });
        }
    }

    #[Action("admin_enqueue_scripts")]
    public function enqueue_backend_assets()
    {
        $vite = $this->vite;
        $js_file = "src/admin.js";

        if (is_array($vite->manifest)) {
            if ($vite->environment === "production" || is_admin()) {
                wp_enqueue_style(
                    "admin",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["css"][0],
                );
                wp_enqueue_script(
                    "admin",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["file"],
                    [],
                    "",
                    [
                        "strategy" => "defer",
                        "in_footer" => true,
                    ],
                );
            }
        }

        if ($vite->environment === "development") {
            add_action("admin_head", function () use ($vite, $js_file) {
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    '@vite/client"></script>';
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    $js_file .
                    '"></script>';
            });
        }
    }

    /**
     * This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    #[Filter("timber/context")]
    public function add_to_context($context)
    {
        $context["site"] = $this;
        $context["menu"] = Timber::get_menu();

        // Set all nav menus in context.
        foreach (array_keys(get_registered_nav_menus()) as $location) {
            // Bail out if menu has no location.
            if (!has_nav_menu($location)) {
                continue;
            }

            $menu = Timber::get_menu($location);
            $context["menus"][$location] = $menu;
        }

        $context["current_url"] = URLHelper::get_current_url();
        $context["environment"] = $this->vite->environment;

        return $context;
    }

    #[Action("after_setup_theme")]
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
    #[Filter("timber/twig")]
    public function add_to_twig($twig)
    {
        $twig->addExtension(new \eSheep\Twigs\Extension());

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
    #[Filter("timber/twig/environment/options")]
    function update_twig_environment_options($options)
    {
        // $options['autoescape'] = true;

        return $options;
    }

    // Redirect non-users to coming soon page, but allow certain other pages
    #[Action("template_redirect")]
    function coming_soon_redirect()
    {
        global $pagenow;

        $is_coming_soon = get_field("enable_coming_soon", "option");

        if (!$is_coming_soon) {
            return;
        }

        $allowed_pages = ["login", "coming-soon"];

        if (
            !is_user_logged_in() &&
            !is_page($allowed_pages) &&
            $pagenow != "wp-login.php"
        ) {
            wp_redirect(home_url("coming-soon"));
            exit();
        }
    }
}
