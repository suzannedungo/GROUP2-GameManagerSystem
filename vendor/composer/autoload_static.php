<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3e4d4fc5dc8d34007a4f0c1678e34c80
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3e4d4fc5dc8d34007a4f0c1678e34c80::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3e4d4fc5dc8d34007a4f0c1678e34c80::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3e4d4fc5dc8d34007a4f0c1678e34c80::$classMap;

        }, null, ClassLoader::class);
    }
}
