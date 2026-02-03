<?php

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
