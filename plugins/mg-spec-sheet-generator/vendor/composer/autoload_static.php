<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitabc7f33e4766f840cc147cb4023272e6
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Process\\' => 26,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'K' => 
        array (
            'Knp\\Snappy\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Knp\\Snappy\\' => 
        array (
            0 => __DIR__ . '/..' . '/knplabs/knp-snappy/src/Knp/Snappy',
        ),
    );

    public static $classMap = array (
        'Knp\\Snappy\\AbstractGenerator' => __DIR__ . '/..' . '/knplabs/knp-snappy/src/Knp/Snappy/AbstractGenerator.php',
        'Knp\\Snappy\\Exception\\FileAlreadyExistsException' => __DIR__ . '/..' . '/knplabs/knp-snappy/src/Knp/Snappy/Exception/FileAlreadyExistsException.php',
        'Knp\\Snappy\\GeneratorInterface' => __DIR__ . '/..' . '/knplabs/knp-snappy/src/Knp/Snappy/GeneratorInterface.php',
        'Knp\\Snappy\\Image' => __DIR__ . '/..' . '/knplabs/knp-snappy/src/Knp/Snappy/Image.php',
        'Knp\\Snappy\\Pdf' => __DIR__ . '/..' . '/knplabs/knp-snappy/src/Knp/Snappy/Pdf.php',
        'Psr\\Log\\AbstractLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/AbstractLogger.php',
        'Psr\\Log\\InvalidArgumentException' => __DIR__ . '/..' . '/psr/log/Psr/Log/InvalidArgumentException.php',
        'Psr\\Log\\LogLevel' => __DIR__ . '/..' . '/psr/log/Psr/Log/LogLevel.php',
        'Psr\\Log\\LoggerAwareInterface' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerAwareInterface.php',
        'Psr\\Log\\LoggerAwareTrait' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerAwareTrait.php',
        'Psr\\Log\\LoggerInterface' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerInterface.php',
        'Psr\\Log\\LoggerTrait' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerTrait.php',
        'Psr\\Log\\NullLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/NullLogger.php',
        'Psr\\Log\\Test\\DummyTest' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/LoggerInterfaceTest.php',
        'Psr\\Log\\Test\\LoggerInterfaceTest' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/LoggerInterfaceTest.php',
        'Symfony\\Component\\Process\\Exception\\ExceptionInterface' => __DIR__ . '/..' . '/symfony/process/Exception/ExceptionInterface.php',
        'Symfony\\Component\\Process\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/symfony/process/Exception/InvalidArgumentException.php',
        'Symfony\\Component\\Process\\Exception\\LogicException' => __DIR__ . '/..' . '/symfony/process/Exception/LogicException.php',
        'Symfony\\Component\\Process\\Exception\\ProcessFailedException' => __DIR__ . '/..' . '/symfony/process/Exception/ProcessFailedException.php',
        'Symfony\\Component\\Process\\Exception\\ProcessSignaledException' => __DIR__ . '/..' . '/symfony/process/Exception/ProcessSignaledException.php',
        'Symfony\\Component\\Process\\Exception\\ProcessTimedOutException' => __DIR__ . '/..' . '/symfony/process/Exception/ProcessTimedOutException.php',
        'Symfony\\Component\\Process\\Exception\\RuntimeException' => __DIR__ . '/..' . '/symfony/process/Exception/RuntimeException.php',
        'Symfony\\Component\\Process\\ExecutableFinder' => __DIR__ . '/..' . '/symfony/process/ExecutableFinder.php',
        'Symfony\\Component\\Process\\InputStream' => __DIR__ . '/..' . '/symfony/process/InputStream.php',
        'Symfony\\Component\\Process\\PhpExecutableFinder' => __DIR__ . '/..' . '/symfony/process/PhpExecutableFinder.php',
        'Symfony\\Component\\Process\\PhpProcess' => __DIR__ . '/..' . '/symfony/process/PhpProcess.php',
        'Symfony\\Component\\Process\\Pipes\\AbstractPipes' => __DIR__ . '/..' . '/symfony/process/Pipes/AbstractPipes.php',
        'Symfony\\Component\\Process\\Pipes\\PipesInterface' => __DIR__ . '/..' . '/symfony/process/Pipes/PipesInterface.php',
        'Symfony\\Component\\Process\\Pipes\\UnixPipes' => __DIR__ . '/..' . '/symfony/process/Pipes/UnixPipes.php',
        'Symfony\\Component\\Process\\Pipes\\WindowsPipes' => __DIR__ . '/..' . '/symfony/process/Pipes/WindowsPipes.php',
        'Symfony\\Component\\Process\\Process' => __DIR__ . '/..' . '/symfony/process/Process.php',
        'Symfony\\Component\\Process\\ProcessUtils' => __DIR__ . '/..' . '/symfony/process/ProcessUtils.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitabc7f33e4766f840cc147cb4023272e6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitabc7f33e4766f840cc147cb4023272e6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitabc7f33e4766f840cc147cb4023272e6::$classMap;

        }, null, ClassLoader::class);
    }
}
