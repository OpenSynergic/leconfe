<?php

namespace App\Utils;

use Illuminate\Support\Arr;

class RequirementChecker 
{
    public function __construct()
    {

    }

    public function php($phpVersion = PHP_VERSION)  : bool
    {
        if (version_compare(app()->getPhpMinVersion(), $phpVersion, '>=')) {
            return false;
        }

        return true;
    }

    public function phpExtensions(array $extensions = []) : array
    {
        $extensions = [
            ...app()->getRequiredPhpExtensions(),
            ...$extensions,
        ];

        return Arr::mapWithKeys($extensions, function($extension, $key){
            return [$extension => extension_loaded($extension)];
        });
    }

    public function isRequirementsMet() : bool
    {
        if(!$this->php()){
            return false;
        }

        foreach($this->phpExtensions() as $extension => $loaded){
            if(!$loaded){
                return false;
            }
        }

        return true;
    }
}