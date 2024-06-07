<?php

namespace App;

class Version
{
    public static function get()
    {
        $version = trim(exec('git describe --tags --abbrev=0'));

        if (!$version) {
            $version = trim(exec('git rev-parse --short HEAD'));
        }

        return $version ?: 'unknown';
    }
}
