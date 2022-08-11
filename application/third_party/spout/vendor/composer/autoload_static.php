<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit44b28f9ade9927bb6cae77d06eb88ab7
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Box\\Spout\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Box\\Spout\\' => 
        array (
            0 => __DIR__ . '/..' . '/box/spout/src/Spout',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit44b28f9ade9927bb6cae77d06eb88ab7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit44b28f9ade9927bb6cae77d06eb88ab7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit44b28f9ade9927bb6cae77d06eb88ab7::$classMap;

        }, null, ClassLoader::class);
    }
}