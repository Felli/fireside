<?php


ET::$pluginInfo["Compatibility"] = array(
    "name"        => "Compatibility",
    "description" => "Compatibility plugin for old esoTalk themes and plugins.",
    "version"     => "1.0",
    "author"      => "Jason Nall",
    "authorEmail" => "jason@dreamhearth.org",
    "authorURL"   => "http://www.jsonnull.com",
    "license"     => "GPLv2"
);

class ETPlugin_Compatibility extends ETPlugin {
    public function handler_init($controller)
    {
        $controller->addJSFile($this->resource("index.js"), true);
    }
}
