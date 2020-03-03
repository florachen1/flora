<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitff24a3e31a63520ecb424b4dbc7ff78f
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'C' => 
        array (
            'Curl' => 
            array (
                0 => __DIR__ . '/..' . '/curl/curl/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitff24a3e31a63520ecb424b4dbc7ff78f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitff24a3e31a63520ecb424b4dbc7ff78f::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitff24a3e31a63520ecb424b4dbc7ff78f::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}