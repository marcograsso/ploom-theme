<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class TinyMCE
{
    public function __construct() {}

    // Sanitize HTML content when pasting in TinyMCE editor.
    #[Filter("tiny_mce_before_init")]
    public function sanitize_tiny_mce_html_content(array $config): array
    {
        $config["paste_preprocess"] = "function(plugin, args) {
        // Allow specific HTML tags while sanitizing the content
        const allowedTags = new Set(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ol', 'ul', 'li', 'a', 'strong', 'em', 'b', 'i']);
        const sanitizedContent = document.createElement('div');
        sanitizedContent.innerHTML = args.content;

        // Remove elements not in the allowed tags
        sanitizedContent.querySelectorAll('*').forEach(element => {
            if (!allowedTags.has(element.tagName.toLowerCase())) {
                element.replaceWith(...element.childNodes); // Replace with child nodes
            }
        });

        // Strip class and id attributes
        sanitizedContent.querySelectorAll('*').forEach(element => {
            element.removeAttribute('id');
            element.removeAttribute('class');
        });

        // Return the clean HTML
        args.content = sanitizedContent.innerHTML;
    }";

        return $config;
    }
}
