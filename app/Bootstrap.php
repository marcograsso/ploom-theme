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
        $this->register_fields();
        $this->register_field_types();
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
            ThemeOptions::class,
            AgeGate::class,
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

    public function require_files_in_folder($folder)
    {
        if (is_dir($folder)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $folder,
                    \RecursiveDirectoryIterator::SKIP_DOTS,
                ),
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === "php") {
                    require_once $file->getPathname();
                }
            }
        }
    }

    public function register_field_types()
    {
        acf_register_field_type(FieldTypes\ACFFieldMapbox::class);
    }

    public function register_fields()
    {
        $folders = [STYLESHEETPATH . "/app/Fields/Groups"];

        foreach ($folders as $folder) {
            $this->require_files_in_folder($folder);
        }
    }
}
