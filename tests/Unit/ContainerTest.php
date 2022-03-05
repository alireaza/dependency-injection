<?php

declare(strict_types=1);

namespace AliReaza\Tests\DependencyInjection\Unit;

use AliReaza\Container\NotFoundException;
use AliReaza\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TypeError;

class ContainerTest extends TestCase
{
    public function test_When_create_new_Container_Expect_Container_instance_of_ContainerInterface(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, new Container());
    }

    public function test_When_create_new_Container_Expect_containers_property_must_array_and_empty(): void
    {
        $container = new Container();

        $this->assertIsArray($container->containers);

        $this->assertEmpty($container->containers);
    }

    public function test_When_container_ID_not_found_Expect_has_method_return_false(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('unregistered'));
    }

    public function test_When_container_ID_not_found_Expect_get_method_throw_NotFoundException(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->get('unregistered');
    }

    public function test_When_set_a_container_with_containers_property_Expect_has_method_return_true_for_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = 'bar';

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_set_a_string_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = 'bar';

        $this->assertSame('bar', $container->get('foo'));
    }

    public function test_When_set_a_integer_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = 1992;

        $this->assertSame(1992, $container->get('foo'));
    }

    public function test_When_set_a_float_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = 10.27;

        $this->assertSame(10.27, $container->get('foo'));
    }

    public function test_When_set_a_boolean_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = true;

        $this->assertSame(true, $container->get('foo'));
    }

    public function test_When_set_a_array_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = ['bar'];

        $this->assertIsArray($container->get('foo'));

        $this->assertSame('bar', $container->get('foo')[0]);
    }

    public function test_When_set_a_callable_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = function (): string {
            return 'bar';
        };

        $this->assertIsCallable($container->get('foo'));

        $this->assertSame('bar', call_user_func($container->get('foo')));
    }

    public function test_When_set_a_class_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->containers['foo'] = new class {
            public function bar(): string
            {
                return 'baz';
            }
        };

        $this->assertIsObject($container->get('foo'));

        $this->assertSame('baz', $container->get('foo')->bar());
    }

    public function test_When_set_and_unset_a_container_with_containers_property_Expect_has_method_return_false(): void
    {
        $container = new Container();

        $container->containers['foo'] = 'bar';
        unset($container->containers['foo']);

        $this->assertFalse($container->has('foo'));
    }

    public function test_When_set_and_unset_a_container_with_containers_property_Expect_get_method_throw_NotFoundException(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->containers['foo'] = 'bar';
        unset($container->containers['foo']);

        $container->get('foo');
    }

    public function test_When_set_a_container_with_set_method_Expect_has_method_return_true_for_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', 'bar');

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_set_a_string_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', 'bar');

        $this->assertSame('bar', $container->get('foo'));
    }

    public function test_When_set_a_integer_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', 1992);

        $this->assertSame(1992, $container->get('foo'));
    }

    public function test_When_set_a_float_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', 10.27);

        $this->assertSame(10.27, $container->get('foo'));
    }

    public function test_When_set_a_boolean_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', true);

        $this->assertSame(true, $container->get('foo'));
    }

    public function test_When_set_a_array_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', ['bar']);

        $this->assertIsArray($container->get('foo'));

        $this->assertSame('bar', $container->get('foo')[0]);
    }

    public function test_When_set_a_callable_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', function (): string {
            return 'bar';
        });

        $this->assertIsCallable($container->get('foo'));

        $this->assertSame('bar', call_user_func($container->get('foo')));
    }

    public function test_When_set_a_class_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new Container();

        $container->set('foo', new class {
            public function bar(): string
            {
                return 'baz';
            }
        });

        $this->assertSame('baz', $container->get('foo')->bar());
    }

    public function test_When_set_a_container_with_set_method_and_non_string_ID_Expect_throw_ErrorException(): void
    {
        $container = new Container();

        $this->expectException(TypeError::class);

        $container->set(123, 'bar');
    }

    public function test_When_set_and_unset_a_container_Expect_has_method_return_false(): void
    {
        $container = new Container();

        $container->set('foo', 'bar');
        $container->unset('foo');

        $this->assertFalse($container->has('foo'));
    }

    public function test_When_set_and_unset_a_container_Expect_get_method_throw_NotFoundException(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->set('foo', 'bar');
        $container->unset('foo');

        $container->get('foo');
    }
}
