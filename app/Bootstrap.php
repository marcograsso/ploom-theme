<?php

namespace App;

use ReflectionClass;
use Timber\Timber;

class Bootstrap
{
    public function __construct()
    {
        Timber::init();
        $this->boot_classes();
    }

    // Initialize all the classes and register their hooks.
    public function boot_classes()
    {
        $classes = [
            Website::class,
            Integrations\AdvancedCustomFields::class,
            Integrations\ACFExtended::class,
            Integrations\TinyMCE::class,
            Integrations\Polylang::class,
            WordPress\WordPress::class,
            WordPress\LoginPage::class,
            WordPress\DisableComments::class,
            WordPress\AdminBar::class,
            WordPress\Dashboard::class,
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
}
