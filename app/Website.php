<?php

namespace App;

use Timber\Site;
use Timber\Timber;
use Timber\URLHelper;
use localghost\Twig\Extra\Hateml\HatemlExtension;
use TalesFromADev\Twig\Extra\Tailwind\TailwindExtension;
use TalesFromADev\Twig\Extra\Tailwind\TailwindRuntime;

use App\Vite;
use App\CustomPostTypes\Example;
use Yard\Hook\Action;
use Yard\Hook\Filter;

use ReflectionClass;

class Website extends Site
{
    public function __construct()
    {
        $this->vite = new Vite();
        $this->register_integrations();
        parent::__construct();
    }

    public function register_integrations()
    {
        $classes = [
            Integrations\AdvancedCustomFields::class,
            Integrations\ACFExtended::class,
            Integrations\TinyMCE::class,
            WordPress\WordPress::class,
            WordPress\LoginPage::class,
            WordPress\DisableComments::class,
        ];

        $classes = collect($classes)
            ->filter(function ($class) {
                if (!is_string($class)) {
                    return false;
                }

                return class_exists($class);
            })
            ->filter(function ($class) {
                $reflectionClass = new ReflectionClass($class);
                $attributes = $reflectionClass->getAttributes(
                    IsPluginActive::class,
                );

                if (count($attributes) === 0) {
                    return true;
                }

                foreach ($attributes as $attribute) {
                    $plugin = $attribute->newInstance();

                    return $plugin->is_active();
                }

                return false;
            })
            ->toArray();

        $hook_registrar = new \Yard\Hook\Registrar($classes);
        $hook_registrar->registerHooks();

        foreach ($classes as $class) {
            new $class();
        }
    }

    #[Action("wp_enqueue_scripts")]
    public function enqueue_frontend_assets()
    {
        // Remove default styles
        global $wp_styles;
        foreach ($wp_styles->queue as $key => $handle) {
            if (strpos($handle, "wp-block-") === 0) {
                wp_dequeue_style($handle);
            }
        }
        wp_dequeue_style("global-styles");
        wp_dequeue_script("jquery");

        if (is_array($this->vite->manifest)) {
            if ($this->vite->environment === "production" || is_admin()) {
                $js_file = "assets/main.js";
                wp_enqueue_style(
                    "main",
                    $this->vite->dist_uri .
                        "/" .
                        $this->vite->manifest[$js_file]["css"][0],
                );
                wp_enqueue_script(
                    "main",
                    $this->vite->dist_uri .
                        "/" .
                        $this->vite->manifest[$js_file]["file"],
                    [],
                    "",
                    [
                        "strategy" => "defer",
                        "in_footer" => true,
                    ],
                );
            }
        }

        if ($this->vite->environment === "development") {
            add_action("wp_head", function () {
                echo '<script type="module" crossorigin src="' .
                    $this->vite->dev_manifest["url"] .
                    '@vite/client"></script>';
                echo '<script type="module" crossorigin src="' .
                    $this->vite->dev_manifest["url"] .
                    'assets/main.js"></script>';
            });
        }
    }

    #[Action("admin_enqueue_scripts")]
    public function enqueue_backend_assets()
    {
        // if (is_array($this->vite->manifest)) {
        //     wp_enqueue_style(
        //         "admin-styles",
        //         $this->vite->dist_uri .
        //             "/" .
        //             $this->vite->manifest["assets/styles/admin.css"]["file"]
        //     );
        // }

        // if ($this->vite->environment === "development") {
        //     add_action("admin_head", function () {
        //         echo '<script type="module" crossorigin src="http://localhost:3000/@vite/client"></script>';
        //         echo '<script type="module" crossorigin src="http://localhost:3000/assets/styles/admin.css"></script>';
        //     });
        // }
    }

    #[Action("init")]
    public function register_custom_post_types()
    {
        Example::register();
    }

    /**
     * This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    #[Filter("timber/context")]
    public function add_to_context($context)
    {
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
        $context["options"] = get_fields("options"); // ACF options
        $context["site"] = $this;
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
        $twig->addExtension(new HatemlExtension());
        $twig->addExtension(new \Twig\Extra\Html\HtmlExtension());
        $twig->addRuntimeLoader(
            new \Twig\RuntimeLoader\FactoryRuntimeLoader([
                TailwindRuntime::class => fn() => new TailwindRuntime(),
            ]),
        );
        $twig->addExtension(new TailwindExtension());

        $twig->addFilter(
            new \Twig\TwigFilter("ray", function (...$params) {
                ray(...$params);
            }),
        );
        $twig->addFunction(
            new \Twig\TwigFunction("ray", function (...$params) {
                ray(...$params);
            }),
        );
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
}
