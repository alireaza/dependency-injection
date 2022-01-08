<?php

declare(strict_types=1);

namespace AliReaza\Tests\DependencyInjection\Unit;

use AliReaza\Container\NotFoundException;
use AliReaza\DependencyInjection\DependencyInjectionContainer;
use Closure;
use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use TypeError;

class anonymous
{
    public function foo(string $text = 'bar'): string
    {
        return $text;
    }
}

class anonymousWithArgumentWithoutHint extends anonymous
{
    public function __construct($argument)
    {
    }
}

class anonymousWithArgumentWithHint extends anonymous
{
    public function __construct(DateTime $argument)
    {
    }
}

/**
 * Class DependencyInjectionContainerTest
 *
 * @package AliReaza\Tests\DependencyInjection\Unit
 */
class DependencyInjectionContainerTest extends TestCase
{
    public function test_When_create_new_DependencyInjectionContainer_Expect_DependencyInjectionContainer_instance_of_ContainerInterface()
    {
        $this->assertInstanceOf(ContainerInterface::class, new DependencyInjectionContainer);
    }

    public function test_When_create_new_DependencyInjectionContainer_Expect_containers_property_must_array_and_empty()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue(is_array($container->containers) && empty($container->containers));
    }

    public function test_When_container_ID_not_found_Expect_has_method_return_false()
    {
        $container = new DependencyInjectionContainer();

        $this->assertFalse($container->has('unregistered'));
    }

    public function test_When_container_ID_not_found_Expect_get_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->get('unregistered');
    }

    public function test_When_adding_a_container_Expect_has_method_return_true_for_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 'bar';

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_non_string_given_to_set_method_Expect_throw_ErrorException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(TypeError::class);

        $container->set(123, 'bar');
    }

    public function test_When_set_a_container_Expect_has_method_return_true_for_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_adding_a_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 'bar';

        $this->assertTrue($container->get('foo') === 'bar');
    }

    public function test_When_set_a_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');

        $this->assertTrue($container->get('foo') === 'bar');
    }

    public function test_When_adding_a_Closure_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = function () {
            return 'bar';
        };

        $this->assertTrue($container->get('foo') instanceof Closure && call_user_func($container->get('foo')) === 'bar');
    }

    public function test_When_set_a_Closure_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', function () {
            return 'bar';
        });

        $this->assertTrue($container->get('foo') instanceof Closure && call_user_func($container->get('foo')) === 'bar');
    }

    public function test_When_adding_a_Class_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = new class {
            public function bar()
            {
            }
        };

        $this->assertTrue(is_object($container->get('foo')) && method_exists($container->get('foo'), 'bar'));
    }

    public function test_When_set_a_Class_container_Expect_get_method_return_the_exact_same_container()
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', new class {
            public function bar()
            {
            }
        });

        $this->assertTrue(is_object($container->get('foo')) && method_exists($container->get('foo'), 'bar'));
    }

    public function test_When_set_and_unset_a_container_Expect_has_method_return_false()
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');
        $container->unset('foo');

        $this->assertFalse($container->has('foo'));
    }

    public function test_When_set_and_unset_a_container_Expect_get_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->set('foo', 'bar');
        $container->unset('foo');

        $container->get('foo');
    }

    public function test_When_set_a_string_as_entry_to_call_method_Expect_call_method_return_the_exact_same_string()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call('foo') === 'foo');
    }

    public function test_When_set_a_integer_as_entry_to_call_method_Expect_call_method_return_the_exact_same_integer()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call(2021) === 2021);
    }

    public function test_When_set_a_stdClass_as_entry_to_call_method_Expect_call_method_return_object_instance_of_stdClass()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call(stdClass::class) instanceof stdClass);
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_Expect_call_method_return_object_instance_of_DateTime()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call(DateTime::class) instanceof DateTime);
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_with_parameters_named_with_the_prefix_dollar_Expect_call_method_return_object_instance_of_DateTime_with_the_value_defined_in_the_parameter()
    {
        $container = new DependencyInjectionContainer();

        $datetime1 = $container->call(DateTime::class, [
            '$datetime' => '1992-10-27 10:15:00'
        ]);

        $datetime2 = new DateTime('1992-10-27 10:15:00');

        $this->assertTrue($datetime1 instanceof $datetime2 && $datetime1->getTimestamp() === $datetime2->getTimestamp());
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_with_parameters_named_without_the_prefix_dollar_Expect_call_method_return_object_instance_of_DateTime_with_the_value_defined_in_the_parameter()
    {
        $container = new DependencyInjectionContainer();

        $datetime1 = $container->call(DateTime::class, [
            'datetime' => '1992-10-27 10:15:00'
        ]);

        $datetime2 = new DateTime('1992-10-27 10:15:00');

        $this->assertTrue($datetime1 instanceof $datetime2 && $datetime1->getTimestamp() === $datetime2->getTimestamp());
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_with_parameters_positioned_with_the_index_Expect_call_method_return_object_instance_of_DateTime_with_the_value_defined_in_the_parameter()
    {
        $container = new DependencyInjectionContainer();

        $datetime1 = $container->call(DateTime::class, [
            0 => '1992-10-27 10:15:00'
        ]);

        $datetime2 = new DateTime('1992-10-27 10:15:00');

        $this->assertTrue($datetime1 instanceof $datetime2 && $datetime1->getTimestamp() === $datetime2->getTimestamp());
    }

    public function test_When_set_a_anonymous_Class_as_entry_to_call_method_Expect_call_method_return_object()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue(is_object($container->call(anonymous::class)));
    }

    public function test_When_set_a_anonymousWithArgumentWithoutHint_Class_as_entry_with_constructor_has_argument_without_hint_type_to_call_method_Expect_call_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(anonymousWithArgumentWithoutHint::class);
    }

    public function test_When_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_Expect_call_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(anonymousWithArgumentWithHint::class);
    }

    public function test_When_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_with_parameter_Expect_call_method_return_object()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue(is_object($container->call(anonymousWithArgumentWithHint::class, [
            '$argument' => new DateTime()
        ])));
    }

    public function test_When_set_DateTime_as_a_container_and_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object()
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertTrue(is_object($container->call(anonymousWithArgumentWithHint::class)));
    }

    public function test_When_use_autowiring_and_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object()
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertTrue(is_object($container->call(anonymousWithArgumentWithHint::class)));
    }

    public function test_When_set_a_array_of_anonymous_Class_and_foo_method_as_entry_to_call_method_Expect_call_method_return_string_from_foo_method()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call([anonymous::class, 'foo']) === 'bar');
    }

    public function test_When_set_a_array_of_anonymousWithArgumentWithoutHint_Class_and_foo_method_as_entry_with_constructor_has_argument_without_hint_type_to_call_method_Expect_call_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call([anonymousWithArgumentWithoutHint::class, 'foo']);
    }

    public function test_When_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_Expect_call_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call([anonymousWithArgumentWithHint::class, 'foo']);
    }

    public function test_When_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_with_parameter_Expect_call_method_return_string_from_foo_method()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call([[anonymousWithArgumentWithHint::class, ['$argument' => new DateTime()]], 'foo'], ['$text' => 'foo_bar']) === 'foo_bar');
    }

    public function test_When_set_DateTime_as_a_container_and_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_return_string_from_foo_method()
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertTrue($container->call([anonymousWithArgumentWithHint::class, 'foo'], ['$text' => 'foo_bar']) === 'foo_bar');
    }

    public function test_When_use_autowiring_and_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_and_return_string_from_foo_method()
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertTrue($container->call([anonymousWithArgumentWithHint::class, 'foo'], ['$text' => 'foo_bar']) === 'foo_bar');
    }

    public function test_When_set_a_anonymous_Closure_as_entry_to_call_method_Expect_call_method_return_strig_from_Closure()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call(fn(): string => 'AliReaza') === 'AliReaza');
    }

    public function test_When_set_a_anonymous_Closure_as_entry_with_argument_without_hint_type_to_call_method_Expect_call_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(fn(DateTime $argument): DateTime => $argument);
    }

    public function test_When_set_a_anonymous_Closure_as_entry_with_argument_with_hint_type_DateTime_to_call_method_Expect_call_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(fn(DateTime $argument): DateTime => $argument);
    }

    public function test_When_set_a_anonymous_Closure_as_entry_with_argument_with_hint_type_DateTime_to_call_method_with_parameter_Expect_call_method_return_object()
    {
        $container = new DependencyInjectionContainer();

        $this->assertTrue($container->call(fn(DateTime $argument): DateTime => $argument, ['$argument' => new DateTime()]) instanceof DateTime);
    }

    public function test_When_set_DateTime_as_a_container_and_set_a_anonymous_Closure_as_entry_with_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object()
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertTrue($container->call(fn(DateTime $argument): DateTime => $argument) instanceof DateTime);
    }

    public function test_When_use_autowiring_and_set_a_anonymous_Closure_as_entry_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object()
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertTrue($container->call(fn(DateTime $argument): DateTime => $argument) instanceof DateTime);
    }

    public function test_When_non_string_given_to_make_method_Expect_throw_ErrorException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(TypeError::class);

        $container->make(123);
    }

    public function test_When_set_DependencyInjectionContainer_Class_as_ID_to_make_method_Expect_make_method_return_self_DependencyInjectionContainer()
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame(spl_object_hash($container), spl_object_hash($container->make(DependencyInjectionContainer::class)));
    }

    public function test_When_container_ID_not_found_Expect_make_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->make(DateTime::class);
    }

    public function test_When_use_autowiring_and_container_ID_not_found_Expect_make_method_inject_instance_of_ID_and_return_instance_of_ID()
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertTrue($container->make(DateTime::class) instanceof DateTime);
    }

    public function test_When_non_string_given_to_resolve_method_Expect_throw_ErrorException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(TypeError::class);

        $container->resolve(123);
    }

    public function test_When_container_ID_not_found_Expect_resolve_method_throw_NotFoundException()
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->resolve(DateTime::class);
    }

    public function test_When_multiple_times_get_instance_from_a_resolve_method_Expect_all_instances_must_same()
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertSame(spl_object_hash($container->resolve(DateTime::class)), spl_object_hash($container->resolve(DateTime::class)));
    }
}