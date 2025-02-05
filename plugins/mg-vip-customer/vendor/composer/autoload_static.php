<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit505a45cd7042469486b265b05565763a
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MG_VIP_Customer\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MG_VIP_Customer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'MG_VIP_Customer\\Includes\\Classes\\ACF' => __DIR__ . '/../..' . '/includes/classes/class-acf.php',
        'MG_VIP_Customer\\Includes\\Classes\\Call_Template_Function' => __DIR__ . '/../..' . '/includes/classes/core/class-call-template-function.php',
        'MG_VIP_Customer\\Includes\\Classes\\Coupon' => __DIR__ . '/../..' . '/includes/classes/class-coupon.php',
        'MG_VIP_Customer\\Includes\\Classes\\Post' => __DIR__ . '/../..' . '/includes/classes/class-post.php',
        'MG_VIP_Customer\\Includes\\Classes\\Table' => __DIR__ . '/../..' . '/includes/classes/class-table.php',
        'MG_VIP_Customer\\Includes\\Classes\\Template' => __DIR__ . '/../..' . '/includes/classes/class-template.php',
        'MG_VIP_Customer\\Includes\\Classes\\Template_Tags' => __DIR__ . '/../..' . '/includes/classes/core/class-template-tags.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit505a45cd7042469486b265b05565763a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit505a45cd7042469486b265b05565763a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit505a45cd7042469486b265b05565763a::$classMap;

        }, null, ClassLoader::class);
    }
}
