<?php

namespace AliReaza\DependencyInjection;

use AliReaza\Singleton\AbstractSingleton;
use AliReaza\Singleton\SingletonInterface;

/**
 * Class DependencyInjectionContainerBuilder
 *
 * @package AliReaza\DependencyInjection
 */
class DependencyInjectionContainerBuilder extends AbstractSingleton implements SingletonInterface
{
    /**
     * @var DependencyInjectionContainer|null
     */
    private static ?DependencyInjectionContainer $instance = null;

    /**
     * @return DependencyInjectionContainer
     */
    public static function getInstance(): DependencyInjectionContainer
    {
        if (is_null(static::$instance)) {
            static::$instance = new DependencyInjectionContainer;
        }

        return static::$instance;
    }
}