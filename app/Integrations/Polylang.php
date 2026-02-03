<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;
use Yard\Hook\Filter;

#[IsPluginActive("polylang-pro/polylang.php")]
class Polylang
{
    #[Filter("timber/twig")]
    public function add_functions_to_twig($twig)
    {
        $twig->addFunction(
            new \Twig\TwigFunction("pll_e", function ($string = "") {
                if (function_exists("pll_e")) {
                    pll_e($string);
                } else {
                    echo $string;
                }
            }),
        );

        $twig->addFunction(
            new \Twig\TwigFunction("pll__", function ($string = "") {
                if (function_exists("pll__")) {
                    pll__($string);
                } else {
                    return $string;
                }
            }),
        );

        return $twig;
    }
}
