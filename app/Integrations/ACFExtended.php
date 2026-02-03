<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;
use Yard\Hook\Action;

#[IsPluginActive("acf-extended-pro/acf-extended.php")]
class ACFExtended
{
    public function __construct() {}

    #[Action("acfe/init")]
    public function enable_classic_editor()
    {
        acfe_update_setting("modules/classic_editor", true);
    }
}
