<?php

class Vite
{
    public $environment = "production";
    public $dist_uri = "";
    public $dist_path = "";
    public $manifest = null;
    public $dev_manifest = null;
    public function __construct()
    {
        $this->dist_uri = get_template_directory_uri() . "/dist";
        $this->dist_path = get_template_directory() . "/dist";

        $this->load_config();
        $this->load_manifest();
        $this->load_dev_manifest();
    }

    public function load_config()
    {
        if (file_exists(get_template_directory() . "/config.json")) {
            $config = json_decode(
                file_get_contents(get_template_directory() . "/config.json"),
                true
            );
            $this->environment = $config["vite"]["environment"] ?? "production";
        }
    }

    public function load_manifest()
    {
        if (file_exists($this->dist_path . "/.vite/manifest.json")) {
            $this->manifest = json_decode(
                file_get_contents($this->dist_path . "/.vite/manifest.json"),
                true
            );
        }
    }

    public function load_dev_manifest()
    {
        if (file_exists($this->dist_path . "/manifest.dev.json")) {
            $this->dev_manifest = json_decode(
                file_get_contents($this->dist_path . "/manifest.dev.json"),
                true
            );
        }
    }
}
