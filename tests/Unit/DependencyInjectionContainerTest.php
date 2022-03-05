<?php

declare(strict_types=1);

namespace AliReaza\Tests\DependencyInjection\Unit;

use AliReaza\Container\NotFoundException;
use AliReaza\DependencyInjection\DependencyInjectionContainer;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
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

class anonymousWithArgumentWithMultipleHint extends anonymous
{
    public function __construct(DateTime|DateTimeImmutable $argument)
    {
    }
}

class DependencyInjectionContainerTest extends TestCase
{
    public function test_When_create_new_DependencyInjectionContainer_Expect_DependencyInjectionContainer_instance_of_ContainerInterface(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, new DependencyInjectionContainer());
    }

    public function test_When_create_new_DependencyInjectionContainer_Expect_containers_property_must_array_and_empty(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertIsArray($container->containers);

        $this->assertEmpty($container->containers);
    }

    public function test_When_container_ID_not_found_Expect_has_method_return_false(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertFalse($container->has('unregistered'));
    }

    public function test_When_container_ID_not_found_Expect_get_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->get('unregistered');
    }

    public function test_When_set_a_container_with_containers_property_Expect_has_method_return_true_for_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 'bar';

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_set_a_string_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 'bar';

        $this->assertSame('bar', $container->get('foo'));
    }

    public function test_When_set_a_integer_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 1992;

        $this->assertSame(1992, $container->get('foo'));
    }

    public function test_When_set_a_float_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 10.27;

        $this->assertSame(10.27, $container->get('foo'));
    }

    public function test_When_set_a_boolean_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = true;

        $this->assertSame(true, $container->get('foo'));
    }

    public function test_When_set_a_array_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = ['bar'];

        $this->assertIsArray($container->get('foo'));

        $this->assertSame('bar', $container->get('foo')[0]);
    }

    public function test_When_set_a_callable_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = function (): string {
            return 'bar';
        };

        $this->assertIsCallable($container->get('foo'));

        $this->assertSame('bar', call_user_func($container->get('foo')));
    }

    public function test_When_set_a_class_container_with_containers_property_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

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
        $container = new DependencyInjectionContainer();

        $container->containers['foo'] = 'bar';
        unset($container->containers['foo']);

        $this->assertFalse($container->has('foo'));
    }

    public function test_When_set_and_unset_a_container_with_containers_property_Expect_get_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->containers['foo'] = 'bar';
        unset($container->containers['foo']);

        $container->get('foo');
    }

    public function test_When_set_a_container_with_set_method_Expect_has_method_return_true_for_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');

        $this->assertTrue($container->has('foo'));
    }

    public function test_When_set_a_string_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');

        $this->assertSame('bar', $container->get('foo'));
    }

    public function test_When_set_a_integer_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 1992);

        $this->assertSame(1992, $container->get('foo'));
    }

    public function test_When_set_a_float_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 10.27);

        $this->assertSame(10.27, $container->get('foo'));
    }

    public function test_When_set_a_boolean_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', true);

        $this->assertSame(true, $container->get('foo'));
    }

    public function test_When_set_a_array_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', ['bar']);

        $this->assertIsArray($container->get('foo'));

        $this->assertSame('bar', $container->get('foo')[0]);
    }

    public function test_When_set_a_callable_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', function (): string {
            return 'bar';
        });

        $this->assertIsCallable($container->get('foo'));

        $this->assertSame('bar', call_user_func($container->get('foo')));
    }

    public function test_When_set_a_class_container_with_set_method_Expect_get_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

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
        $container = new DependencyInjectionContainer();

        $this->expectException(TypeError::class);

        $container->set(123, 'bar');
    }

    public function test_When_set_and_unset_a_container_Expect_has_method_return_false(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');
        $container->unset('foo');

        $this->assertFalse($container->has('foo'));
    }

    public function test_When_set_and_unset_a_container_Expect_get_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->set('foo', 'bar');
        $container->unset('foo');

        $container->get('foo');
    }

    public function test_When_set_a_string_container_with_set_method_Expect_resolve_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');

        $this->assertSame('bar', $container->resolve('foo'));
    }

    public function test_When_set_a_integer_container_with_set_method_Expect_resolve_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 1992);

        $this->assertSame(1992, $container->resolve('foo'));
    }

    public function test_When_set_a_float_container_with_set_method_Expect_resolve_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 10.27);

        $this->assertSame(10.27, $container->resolve('foo'));
    }

    public function test_When_set_a_boolean_container_with_set_method_Expect_resolve_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', true);

        $this->assertSame(true, $container->resolve('foo'));
    }

    public function test_When_set_a_array_container_with_set_method_Expect_resolve_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', ['bar']);

        $this->assertIsArray($container->resolve('foo'));

        $this->assertSame('bar', $container->resolve('foo')[0]);
    }

    public function test_When_set_a_callable_array_container_with_set_method_Expect_resolve_method_return_the_output_of_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', [anonymous::class, 'foo']);

        $this->assertSame('bar', $container->resolve('foo'));
    }

    public function test_When_set_a_callable_container_with_set_method_Expect_resolve_method_return_the_output_of_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', function (): string {
            return 'bar';
        });

        $this->assertSame('bar', $container->resolve('foo'));
    }

    public function test_When_set_a_class_container_with_set_method_Expect_resolve_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', new class {
            public function bar(): string
            {
                return 'baz';
            }
        });

        $this->assertIsObject($container->resolve('foo'));

        $this->assertTrue(method_exists($container->resolve('foo'), 'bar'));

        $this->assertSame('baz', $container->resolve('foo')->bar());
    }

    public function test_When_set_a_string_container_with_set_method_Expect_make_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 'bar');

        $this->assertSame('bar', $container->make('foo'));
    }

    public function test_When_set_a_integer_container_with_set_method_Expect_make_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 1992);

        $this->assertSame(1992, $container->make('foo'));
    }

    public function test_When_set_a_float_container_with_set_method_Expect_make_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', 10.27);

        $this->assertSame(10.27, $container->make('foo'));
    }

    public function test_When_set_a_boolean_container_with_set_method_Expect_make_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', true);

        $this->assertSame(true, $container->make('foo'));
    }

    public function test_When_set_a_array_container_with_set_method_Expect_make_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', ['bar']);

        $this->assertIsArray($container->make('foo'));

        $this->assertSame('bar', $container->make('foo')[0]);
    }

    public function test_When_set_a_callable_array_container_with_set_method_Expect_make_method_return_the_output_of_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', [anonymous::class, 'foo']);

        $this->assertSame('bar', $container->make('foo'));
    }

    public function test_When_set_a_callable_container_with_set_method_Expect_make_method_return_the_output_of_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', function (): string {
            return 'bar';
        });

        $this->assertSame('bar', $container->make('foo'));
    }

    public function test_When_set_a_class_container_with_set_method_Expect_make_method_return_the_exact_same_container(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set('foo', new class {
            public function bar(): string
            {
                return 'baz';
            }
        });

        $this->assertIsObject($container->make('foo'));

        $this->assertTrue(method_exists($container->make('foo'), 'bar'));

        $this->assertSame('baz', $container->make('foo')->bar());
    }

    public function test_When_non_string_given_to_make_method_Expect_throw_ErrorException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(TypeError::class);

        $container->make(123);
    }

    public function test_When_set_a_string_as_entry_to_call_method_Expect_call_method_return_the_exact_string(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame('foo', $container->call('foo'));
    }

    public function test_When_set_a_integer_as_entry_to_call_method_Expect_call_method_return_the_exact_same_integer(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame(1992, $container->call(1992));
    }

    public function test_When_set_a_float_as_entry_to_call_method_Expect_call_method_return_the_exact_same_float(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame(10.27, $container->call(10.27));
    }

    public function test_When_set_a_boolean_as_entry_to_call_method_Expect_call_method_return_the_exact_same_boolean(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame(true, $container->call(true));
    }

    public function test_When_set_a_array_as_entry_to_call_method_Expect_call_method_return_the_exact_same_array(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertIsArray($container->call(['bar']));

        $this->assertSame('bar', $container->call(['bar'])[0]);
    }

    public function test_When_set_a_callable_as_entry_to_call_method_Expect_call_method_return_the_output_of_callable(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame('bar', $container->call(function (): string {
            return 'bar';
        }));
    }

    public function test_When_set_a_class_as_entry_to_call_method_Expect_call_method_return_the_exact_same_class(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertIsObject($container->call(new class {
            public function bar(): string
            {
                return 'baz';
            }
        }));

        $this->assertTrue(method_exists($container->call(new class {
            public function bar(): string
            {
                return 'baz';
            }
        }), 'bar'));

        $this->assertSame('baz', $container->call(new class {
            public function bar(): string
            {
                return 'baz';
            }
        })->bar());
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_Expect_call_method_return_object_instance_of_DateTime(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertInstanceOf(DateTime::class, $container->call(DateTime::class));
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_with_parameters_named_with_the_prefix_dollar_Expect_call_method_return_object_instance_of_DateTime_with_the_value_defined_in_the_parameter(): void
    {
        $container = new DependencyInjectionContainer();

        $datetime1 = $container->call(DateTime::class, [
            '$datetime' => '1992-10-27 10:15:00'
        ]);

        $datetime2 = new DateTime('1992-10-27 10:15:00');

        $this->assertInstanceOf($datetime2::class, $datetime1);

        $this->assertSame($datetime2->getTimestamp(), $datetime1->getTimestamp());
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_with_parameters_named_without_the_prefix_dollar_Expect_call_method_return_object_instance_of_DateTime_with_the_value_defined_in_the_parameter(): void
    {
        $container = new DependencyInjectionContainer();

        $datetime1 = $container->call(DateTime::class, [
            'datetime' => '1992-10-27 10:15:00'
        ]);

        $datetime2 = new DateTime('1992-10-27 10:15:00');

        $this->assertInstanceOf($datetime2::class, $datetime1);

        $this->assertSame($datetime2->getTimestamp(), $datetime1->getTimestamp());
    }

    public function test_When_set_a_DateTime_class_as_entry_to_call_method_with_parameters_positioned_with_the_index_Expect_call_method_return_object_instance_of_DateTime_with_the_value_defined_in_the_parameter(): void
    {
        $container = new DependencyInjectionContainer();

        $datetime1 = $container->call(DateTime::class, [
            0 => '1992-10-27 10:15:00'
        ]);

        $datetime2 = new DateTime('1992-10-27 10:15:00');

        $this->assertInstanceOf($datetime2::class, $datetime1);

        $this->assertSame($datetime2->getTimestamp(), $datetime1->getTimestamp());
    }

    public function test_When_set_a_anonymous_Class_as_entry_to_call_method_Expect_call_method_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertIsObject($container->call(anonymous::class));
    }

    public function test_When_set_a_anonymousWithArgumentWithoutHint_Class_as_entry_with_constructor_has_argument_without_hint_type_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(anonymousWithArgumentWithoutHint::class);
    }

    public function test_When_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(anonymousWithArgumentWithHint::class);
    }

    public function test_When_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_with_parameter_Expect_call_method_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertIsObject($container->call(anonymousWithArgumentWithHint::class, [
            '$argument' => new DateTime()
        ]));
    }

    public function test_When_set_DateTime_as_a_container_and_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertIsObject($container->call(anonymousWithArgumentWithHint::class));
    }

    public function test_When_use_autowiring_and_set_a_anonymousWithArgumentWithHint_Class_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertIsObject($container->call(anonymousWithArgumentWithHint::class));
    }

    public function test_When_set_a_array_of_anonymous_Class_and_foo_method_as_entry_to_call_method_Expect_call_method_return_string_from_foo_method(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame('bar', $container->call([anonymous::class, 'foo']));
    }

    public function test_When_set_a_array_of_anonymousWithArgumentWithoutHint_Class_and_foo_method_as_entry_with_constructor_has_argument_without_hint_type_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call([anonymousWithArgumentWithoutHint::class, 'foo']);
    }

    public function test_When_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call([anonymousWithArgumentWithHint::class, 'foo']);
    }

    public function test_When_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_hint_type_DateTime_to_call_method_with_parameter_Expect_call_method_return_string_from_foo_method(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame('foo_bar', $container->call([[anonymousWithArgumentWithHint::class, ['$argument' => new DateTime()]], 'foo'], ['$text' => 'foo_bar']));
    }

    public function test_When_set_DateTime_as_a_container_and_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_return_string_from_foo_method(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertSame('foo_bar', $container->call([anonymousWithArgumentWithHint::class, 'foo'], ['$text' => 'foo_bar']));
    }

    public function test_When_use_autowiring_and_set_a_array_of_anonymousWithArgumentWithHint_Class_and_foo_method_as_entry_with_constructor_has_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_and_return_string_from_foo_method(): void
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertSame('foo_bar', $container->call([anonymousWithArgumentWithHint::class, 'foo'], ['$text' => 'foo_bar']));
    }

    public function test_When_set_a_anonymousWithArgumentWithMultipleHint_Class_as_entry_with_constructor_has_argument_with_multiple_hint_types_DateTime_and_DateTimeImmutable_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(anonymousWithArgumentWithMultipleHint::class);
    }

    public function test_When_set_a_anonymousWithArgumentWithMultipleHint_Class_as_entry_with_constructor_has_argument_with_multiple_hint_types_DateTime_and_DateTimeImmutable_to_call_method_with_parameter_Expect_call_method_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertIsObject($container->call(anonymousWithArgumentWithMultipleHint::class, [
            '$argument' => new DateTimeImmutable()
        ]));
    }

    public function test_When_set_a_anonymous_Closure_as_entry_to_call_method_Expect_call_method_return_string_from_Closure(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame('AliReaza', $container->call(fn(): string => 'AliReaza'));
    }

    public function test_When_set_a_anonymous_Closure_as_entry_with_argument_without_hint_type_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(fn($argument): DateTime => $argument);
    }

    public function test_When_set_a_anonymous_Closure_as_entry_with_argument_with_hint_type_DateTime_to_call_method_Expect_call_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->call(fn(DateTime $argument): DateTime => $argument);
    }

    public function test_When_set_a_anonymous_Closure_as_entry_with_argument_with_hint_type_DateTime_to_call_method_with_parameter_Expect_call_method_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertInstanceOf(DateTime::class, $container->call(fn(DateTime $argument): DateTime => $argument, ['$argument' => new DateTime()]));
    }

    public function test_When_set_DateTime_as_a_container_and_set_a_anonymous_Closure_as_entry_with_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertInstanceOf(DateTime::class, $container->call(fn(DateTime $argument): DateTime => $argument));
    }

    public function test_When_use_autowiring_and_set_a_anonymous_Closure_as_entry_argument_with_type_DateTime_to_call_method_Expect_call_method_inject_DateTime_return_object(): void
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertInstanceOf(DateTime::class, $container->call(fn(DateTime $argument): DateTime => $argument));
    }

    public function test_When_set_DependencyInjectionContainer_Class_as_ID_to_make_method_Expect_make_method_return_self_DependencyInjectionContainer(): void
    {
        $container = new DependencyInjectionContainer();

        $this->assertSame(spl_object_hash($container), spl_object_hash($container->make(DependencyInjectionContainer::class)));
    }

    public function test_When_container_ID_not_found_Expect_make_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->make(DateTime::class);
    }

    public function test_When_use_autowiring_and_container_ID_not_found_Expect_make_method_inject_instance_of_ID_and_return_instance_of_ID(): void
    {
        $container = new DependencyInjectionContainer();

        $container->useAutowiring(true);

        $this->assertInstanceOf(DateTime::class, $container->make(DateTime::class));
    }

    public function test_When_non_string_given_to_resolve_method_Expect_throw_ErrorException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(TypeError::class);

        $container->resolve(123);
    }

    public function test_When_container_ID_not_found_Expect_resolve_method_throw_NotFoundException(): void
    {
        $container = new DependencyInjectionContainer();

        $this->expectException(NotFoundException::class);

        $container->resolve(DateTime::class);
    }

    public function test_When_multiple_times_get_instance_from_a_resolve_method_Expect_all_instances_must_same(): void
    {
        $container = new DependencyInjectionContainer();

        $container->set(DateTime::class, DateTime::class);

        $this->assertSame(spl_object_hash($container->resolve(DateTime::class)), spl_object_hash($container->resolve(DateTime::class)));
    }
}
