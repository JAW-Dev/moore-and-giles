<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7dedbae104870150c692c99b43cf8e66
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MgAdmin\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MgAdmin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/classes',
        ),
    );

    public static $classMap = array (
        'MgAdmin\\Includes\\Classes\\Activation' => __DIR__ . '/../..' . '/includes/classes/Activation.php',
        'MgAdmin\\Includes\\Classes\\AdminPages\\Init' => __DIR__ . '/../..' . '/includes/classes/AdminPages/Init.php',
        'MgAdmin\\Includes\\Classes\\AdminPages\\OrderCategoryList' => __DIR__ . '/../..' . '/includes/classes/AdminPages/OrderCategoryList.php',
        'MgAdmin\\Includes\\Classes\\AdminTabels\\Init' => __DIR__ . '/../..' . '/includes/classes/AdminTabels/Init.php',
        'MgAdmin\\Includes\\Classes\\AdminTabels\\OrderCategoryList' => __DIR__ . '/../..' . '/includes/classes/AdminTabels/OrderCategoryList.php',
        'MgAdmin\\Includes\\Classes\\Deactivation' => __DIR__ . '/../..' . '/includes/classes/Deactivation.php',
        'MgAdmin\\Includes\\Classes\\Init' => __DIR__ . '/../..' . '/includes/classes/Init.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7dedbae104870150c692c99b43cf8e66::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7dedbae104870150c692c99b43cf8e66::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7dedbae104870150c692c99b43cf8e66::$classMap;

        }, null, ClassLoader::class);
    }
}
