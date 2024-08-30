<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit125f028c7c80d1cd781f5e0777c9079b
{
    public static $files = array (
        '57c3f1b17b10b0fd7a362199706b4ff1' => __DIR__ . '/../..' . '/includes/functions/render_cart_addons.php',
    );

    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MG_Product_Addons\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MG_Product_Addons\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Classes',
        ),
    );

    public static $classMap = array (
        'MG_Product_Addons\\Includes\\Classes\\AddCartItemPrice' => __DIR__ . '/../..' . '/includes/Classes/AddCartItemPrice.php',
        'MG_Product_Addons\\Includes\\Classes\\Cart' => __DIR__ . '/../..' . '/includes/Classes/Cart.php',
        'MG_Product_Addons\\Includes\\Classes\\CartAddons' => __DIR__ . '/../..' . '/includes/Classes/CartAddons.php',
        'MG_Product_Addons\\Includes\\Classes\\CreateFieldGroup' => __DIR__ . '/../..' . '/includes/Classes/ACF/CreateFieldGroup.php',
        'MG_Product_Addons\\Includes\\Classes\\CreateOptionsPage' => __DIR__ . '/../..' . '/includes/Classes/ACF/CreateOptionsPage.php',
        'MG_Product_Addons\\Includes\\Classes\\GetItemData' => __DIR__ . '/../..' . '/includes/Classes/GetItemData.php',
        'MG_Product_Addons\\Includes\\Classes\\GetPost' => __DIR__ . '/../..' . '/includes/Classes/GetPost.php',
        'MG_Product_Addons\\Includes\\Classes\\OrderLineItem' => __DIR__ . '/../..' . '/includes/Classes/OrderLineItem.php',
        'MG_Product_Addons\\Includes\\Classes\\ProductAddons' => __DIR__ . '/../..' . '/includes/Classes/ProductAddons.php',
        'MG_Product_Addons\\Includes\\Classes\\RenderFields' => __DIR__ . '/../..' . '/includes/Classes/RenderFields.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit125f028c7c80d1cd781f5e0777c9079b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit125f028c7c80d1cd781f5e0777c9079b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit125f028c7c80d1cd781f5e0777c9079b::$classMap;

        }, null, ClassLoader::class);
    }
}
