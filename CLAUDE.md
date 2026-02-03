# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build Commands

```bash
npm run dev       # Start Vite dev server with HMR (https://localhost:3000)
npm run build     # Production build to /dist
npm run svgo      # Optimize SVG icons in views/icons/

composer install  # Install PHP dependencies
```

Requires [jaq](https://github.com/01mf02/jaq) installed for config.json manipulation.

## Architecture

WordPress theme using **Timber v2** (Twig templating) + **Vite** (build tooling) + **Tailwind CSS v4** + **Alpine.js**.

### PHP (app/)

- **Bootstrap.php**: Entry point. Initializes Timber, boots all service classes, registers ACF field groups from `app/Fields/Groups/`
- **Website.php**: Extends `Timber\Site`. Handles asset enqueueing, Twig context, theme supports, Twig extensions
- **Vite.php**: Asset manifest handling. Reads `config.json` for environment (development/production) and loads Vite manifests

Classes use PHP 8 attributes for WordPress hooks via `yard/wp-hook-registrar`:
```php
#[Action("init")]
public function register_post_types() { ... }

#[Filter("timber/context")]
public function add_to_context($context) { ... }
```

Use `#[IsPluginActive("plugin/plugin.php")]` attribute on classes to conditionally load them only when a plugin is active.

### Post Types & Taxonomies

- Extend `Timber\Post` in `app/PostTypes/`
- Use `register_extended_post_type()` from Extended CPTs
- Register in `Website::register_post_types()` via `#[Action("init")]`
- ACF fields defined inline using Extended ACF fluent API

### ACF Field Groups

- Standalone groups go in `app/Fields/Groups/` (auto-loaded)
- Post type fields can be registered inline in the post type class
- Uses `vinkla/extended-acf` for fluent field definitions

### Twig Templates (views/)

- `base.twig`: Base layout with head, body, content/footer blocks
- `templates/`: Page templates (page.twig, single.twig, archive.twig, 404.twig)
- `parts/`: Reusable partials (head.twig, footer.twig, pagination.twig)
- `components/`: UI components (button.twig, carousel.twig)
- `icons/`: SVG icons (optimized via svgo)

Template naming: `page-{id}.twig`, `page-{slug}.twig`, then `page.twig`

### Frontend (src/)

- **main.js/main.css**: Frontend entry point. Imports Alpine.js, component JS, Tailwind
- **admin.js/admin.css**: WP admin entry point

Vite auto-imports JS/CSS from `views/` directories via `import.meta.glob`.

### Vite Integration

- Dev server runs on https://localhost:3000 with HMR
- `config.json` tracks current environment (development/production)
- Production manifests in `dist/.vite/manifest.json`
- Dev manifest in `dist/manifest.dev.json`

## Key Libraries

| Library | Purpose |
|---------|---------|
| timber/timber | Twig templating for WordPress |
| vinkla/extended-acf | Fluent ACF field definitions |
| johnbillion/extended-cpts | Fluent CPT/taxonomy registration |
| yard/wp-hook-registrar | PHP 8 attribute-based hook registration |
| spatie/ray | Debug tool (ray() function in PHP/Twig) |
| localghost/twig-hateml | HTML abstraction Twig extension |
