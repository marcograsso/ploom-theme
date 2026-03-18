<?php

namespace App\FieldTypes;

use Extended\ACF\Fields\Field;
use Extended\ACF\Fields\Settings\HelperText;
use Extended\ACF\Fields\Settings\Required;
use Extended\ACF\Fields\Settings\Wrapper;

class Mapbox extends Field
{
    use HelperText;
    use Required;
    use Wrapper;

    protected ?string $type = "mapbox";

    public function mapbox_api_key(string $mapbox_api_key): static
    {
        $this->settings["mapbox_api_key"] = $mapbox_api_key;

        return $this;
    }

    public function default_country(string $default_country): static
    {
        $this->settings["default_country"] = $default_country;

        return $this;
    }
}
