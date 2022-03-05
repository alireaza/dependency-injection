<?php

namespace AliReaza\DependencyInjection;

use AliReaza\Singleton\AbstractSingleton;
use AliReaza\Singleton\SingletonInterface;

class DependencyInjectionContainerBuilder extends AbstractSingleton implements SingletonInterface
{
    private static ?DependencyInjectionContainer $instance = null;

    public static function getInstance(): DependencyInjectionContainer
    {
        if (is_null(static::$instance)) {
            static::$instance = new DependencyInjectionContainer();
        }

        return static::$instance;
    }
}
