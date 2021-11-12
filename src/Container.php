<?php

namespace AliReaza\DependencyInjection;

use AliReaza\Container\Container as PsrContainer;

/**
 * Class Container
 *
 * @package AliReaza\DependencyInjection
 */
class Container extends PsrContainer
{
    /**
     * @param string $id
     * @param mixed $entry
     */
    public function set(string $id, mixed $entry): void
    {
        $this->containers[$id] = $entry;
    }

    /**
     * @param string $id
     */
    public function unset(string $id): void
    {
        unset($this->containers[$id]);
    }
}