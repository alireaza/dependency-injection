<?php

declare(strict_types=1);

namespace AliReaza\Tests\DependencyInjection\Unit;

use AliReaza\Container\NotFoundException;
use AliReaza\DependencyInjection\Container;
use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TypeError;

/**
 * Class ContainerTest
 *
 * @package AliReaza\Tests\DependencyInjection\Unit
 */
class ContainerTest extends TestCase
{
    public function test_When_create_new_Container_Expect_Container_instance_of_ContainerInterface()
    {
        $this->assertInstanceOf(ContainerInterface::class, new Container);
    }

    public function test_When_create_new_Container_Expect_containers_property_must_array_and_empty()
    {
        $container = new Container();

        $this->assertTrue(is_array($container->containers) && empty($container->containers));
    }

    public function test_When_container_ID_not_found_Expect_has_method_return_false()
    {
        $container = new Container();

        $this->assertFalse($container->has('unregistered'));
    }

    public function test_When_container_ID_not_found_Expect_get_method_throw_NotFoundException()
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->get('unregistered');
    }

    public function test_When_adding_a_container_Expect_has_method_return_true_for_the_exact_same_container()
    {
        $container = new Container();

        $container->containers['foo'] = 'bar';

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_non_string_given_to_set_method_Expect_throw_ErrorException()
    {
        $container = new Container();

        $this->expectException(TypeError::class);

        $container->set(123, 'bar');
    }

    public function test_When_set_a_container_Expect_has_method_return_true_for_the_exact_same_container()
    {
        $container = new Container();

        $container->set('foo', 'bar');

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_adding_a_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new Container();

        $container->containers['foo'] = 'bar';

        $this->assertTrue($container->get('foo') === 'bar');
    }

    public function test_When_set_a_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new Container();

        $container->set('foo', 'bar');

        $this->assertTrue($container->get('foo') === 'bar');
    }

    public function test_When_adding_a_Closure_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new Container();

        $container->containers['foo'] = function () {
            return 'bar';
        };

        $this->assertTrue($container->get('foo') instanceof Closure && call_user_func($container->get('foo')) === 'bar');
    }

    public function test_When_set_a_Closure_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new Container();

        $container->set('foo', function () {
            return 'bar';
        });

        $this->assertTrue($container->get('foo') instanceof Closure && call_user_func($container->get('foo')) === 'bar');
    }

    public function test_When_adding_a_Class_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new Container();

        $container->containers['foo'] = new class {
            public function bar()
            {
            }
        };

        $this->assertTrue(is_object($container->get('foo')) && method_exists($container->get('foo'), 'bar'));
    }

    public function test_When_set_a_Class_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new Container();

        $container->set('foo', new class {
            public function bar()
            {
            }
        });

        $this->assertTrue(is_object($container->get('foo')) && method_exists($container->get('foo'), 'bar'));
    }

    public function test_When_set_and_unset_a_container_Expect_has_method_return_false()
    {
        $container = new Container();

        $container->set('foo', 'bar');
        $container->unset('foo');

        $this->assertFalse($container->has('foo'));
    }

    public function test_When_set_and_unset_a_container_Expect_get_method_throw_NotFoundException()
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->set('foo', 'bar');
        $container->unset('foo');

        $container->get('foo');
    }
}