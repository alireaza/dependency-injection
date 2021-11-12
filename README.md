# Dependency Injection Container


## Install

Via Composer
```bash
$ composer require alireaza/dependency-injection
```


## Usage

```php
use AliReaza\DependencyInjection\DependencyInjectionContainer;

$dic = new DependencyInjectionContainer();

$dic->useAutowiring(true);

$alireaza_birthday = $dic->resolve(DateTime::class, [
    '$datetime' => '1992-10-27 10:15:00'
]);
```


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.