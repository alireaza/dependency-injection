<?php

namespace AliReaza\DependencyInjection;

use AliReaza\Container\Container as PsrContainer;

class Container extends PsrContainer
{
    public function set(string $id, mixed $entry): void
    {
        $this->containers[$id] = $entry;
    }

    public function unset(string $id): void
    {
        unset($this->containers[$id]);
    }
}
