<?php

// autoload_real.php generated by Composer

class ComposerAutoloaderInit617778089de7b63e04b100316f73dd6c
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit617778089de7b63e04b100316f73dd6c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit617778089de7b63e04b100316f73dd6c', 'loadClassLoader'));

        $vendorDir = dirname(__DIR__);
        $baseDir = dirname(dirname(dirname(dirname(dirname($vendorDir))))).'/php/project/micblr/ZendSkeletonApplication';

        $map = require __DIR__ . '/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->add($namespace, $path);
        }

        $classMap = require __DIR__ . '/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }

        $loader->register(true);

        return $loader;
    }
}
