<?php

declare(strict_types=1);

namespace AliReaza\Tests\DependencyInjection\Unit;

use AliReaza\DependencyInjection\DependencyInjectionContainer;
use AliReaza\DependencyInjection\DependencyInjectionContainerBuilder;
use AliReaza\Singleton\AbstractSingleton;
use AliReaza\Singleton\SingletonInterface;
use PHPUnit\Framework\TestCase;

class DependencyInjectionContainerBuilderTest extends TestCase
{
    public function test_When_create_new_DependencyInjectionContainerBuilder_Expect_DependencyInjectionContainerBuilder_instance_of_SingletonInterface(): void
    {
        $this->expectError();

        $this->assertInstanceOf(SingletonInterface::class, new DependencyInjectionContainerBuilder());
    }

    public function test_When_create_new_DependencyInjectionContainerBuilder_Expect_DependencyInjectionContainerBuilder_instance_of_AbstractSingleton(): void
    {
        $this->expectError();

        $this->assertInstanceOf(AbstractSingleton::class, new DependencyInjectionContainerBuilder());
    }

    public function test_When_create_new_DependencyInjectionContainerBuilder_Expect_throw_exception_for_protected_constructor(): void
    {
        $this->expectErrorMessageMatches('/^Call to protected/');

        new DependencyInjectionContainerBuilder();
    }

    public function test_When_extend_DependencyInjectionContainerBuilder_Expect_throw_exception_for_protected_constructor(): void
    {
        $this->expectErrorMessageMatches('/^Call to protected/');

        new class extends DependencyInjectionContainerBuilder {
        };
    }

    public function test_When_extend_DependencyInjectionContainerBuilder_with_public_constructor_with_arguments_Expect_throw_exception_ArgumentCountError(): void
    {
        $this->expectErrorMessageMatches('/^Too few arguments to function/');

        new class extends DependencyInjectionContainerBuilder {
            public function __construct($arg)
            {
            }
        };
    }

    public function test_When_get_instance_from_a_DependencyInjectionContainerBuilder_Expect_instance_of_DependencyInjectionContainer(): void
    {
        $this->expectError();

        $this->assertInstanceOf(DependencyInjectionContainer::class, new DependencyInjectionContainerBuilder());
    }

    public function test_When_multiple_times_get_instance_from_a_DependencyInjectionContainerBuilder_Expect_all_instances_must_same(): void
    {
        $obj1 = DependencyInjectionContainerBuilder::getInstance();
        $obj2 = DependencyInjectionContainerBuilder::getInstance();

        $this->assertSame(spl_object_hash($obj1), spl_object_hash($obj2));
    }
}
