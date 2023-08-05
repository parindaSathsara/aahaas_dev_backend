<?php

namespace App\Http\Controllers\Sabre;

use Illuminate\Support\Facades\Storage;

class SabreFlightConfig
{

    private $restConfig;
    private static $instance = null;

    private function __construct()
    {
        $this->restConfig = parse_ini_file("configurations/SabreFlightConfig.ini");
    }

    public static function getInstance()
    {
        if (SabreFlightConfig::$instance === null) {
            SabreFlightConfig::$instance = new SabreFlightConfig();
        }
        return SabreFlightConfig::$instance;
    }

    public function getRestProperty($propertyName)
    {
        return $this->restConfig[$propertyName];
    }
}
