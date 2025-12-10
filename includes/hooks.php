<?php

add_action("acf/init", "my_acfe_modules");
function my_acfe_modules()
{
    // Enable Classic Editor
    acf_update_setting("acfe/modules/classic_editor", true);

    // Enable Force Sync
    acf_update_setting("acfe/modules/force_sync", true);

    // Enable Force Sync Deleted Json
    acf_update_setting("acfe/modules/force_sync/delete", true);
}

// Fixes site previews when shared on Discord
add_filter(
    "oembed_response_data",
    "disable_embeds_filter_oembed_response_data_",
);
function disable_embeds_filter_oembed_response_data_($data)
{
    unset($data["author_url"]);
    unset($data["author_name"]);
    return $data;
}

// modify the path to the icons directory
add_filter("acf_icon_path_suffix", "acf_icon_path_suffix");
function acf_icon_path_suffix($path_suffix)
{
    return "views/svgs/";
}
