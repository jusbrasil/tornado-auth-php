<img align="right" height="48" src="https://user-images.githubusercontent.com/5435389/62345208-ba623280-b4c7-11e9-8fc4-2647accfc306.png" style="padding-top: 16px">

# Tornado Auth PHP

Implementation of Tornado Web Authentication in PHP.

<p align="center"><img src="https://user-images.githubusercontent.com/5435389/62345012-db765380-b4c6-11e9-834f-df22ee20ae39.jpg" height="256" /></p>

## Usage

> namespace

```php
use Jusbrasil\TornadoAuthPhp\TornadoAuthPhpLib;
```

### `configure ( array[string] mixed $options ) : void`

* `max_age_days` - Number of days that the signature is valid.
* `user_cookie` - The name of the cookie.
* `secret_key` - The secret key used to decrypt the signed value.

```php
$auth = new TornadoAuthPhpLib([
  'max_age_days' => 2,      // [optional] default: (int) 31
  'user_cookie' => 'oreo',  // [optional] default: (string) user
  'secret_key' => 'shhhh',  // [required]
]);

// void
```

### `createSignedValue ( mixed $value ) : string`

Sign the passed value.

```php
$signedValue = $auth->createSignedValue(['cypher' => 'morpheus']);

// string(80) "eyJjeXBoZXIiOiJtb3JwaGV1cyJ9|1564713616|ca4f8c77f23f120578e742199b12df21f6039ce3"
```

### `createSignedCookie( string $cookieName, mixed $value ) : string`

Sign the passed value.

```php
$signedCookie = $auth->createSignedCookie('oreo', ['cypher' => 'morpheus']);

// string(80) "eyJjeXBoZXIiOiJtb3JwaGV1cyJ9|1564713616|07143659017c55c004108de1e8b3867a8a5a889d"
```

### `decodeSignedValue ( string $secret, string $name, string $value [, int $maxAgeDays ] ) : string`

Decode the signed value into string.

```php
$decodedSignedValue = $auth->decodeSignedValue('shhhh', 'oreo', $signedValue);

// string(21) "{"cypher":"morpheus"}"
```

### `getSecureCookie ( string $cookieName, string $value [, int $maxAgeDays ] ) : object`

```php
$secureCookie = $auth->getSecureCookie('oreo', $signedValue);

// class stdClass {
//   public $cypher => string(8) "morpheus"
// }
```

### `getCurrentUser ( mixed $value [, int $maxAgeDays ] ) : object`

```php
$currentUser = $auth->getCurrentUser($signedValue);

// class stdClass {
//   public $cypher => string(8) "morpheus"
// }
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

### Prerequisites

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/)

### Installing

Clone the repository

    git clone git@github.com:jusbrasil/tornado-auth-php.git

Set up the development environment

    docker-compose up -d --build

Access the container

    docker-compose exec app ash

Or, run directly the command that you want (e.g.)

    docker-compose exec app php src/index.php

[//]: # (### Running the tests)

[//]: # (TODO: Explain how to run the automated tests for this system)

### Versioning

We use [SemVer](http://semver.org/) for versioning. Given a version number MAJOR.MINOR.PATCH, increment the:

1. `MAJOR` major changes or a defined group of features that belongs to the same scope
2. `MINOR` a new feature or refactoring an existing feature
3. `PATCH` fixing a bug or improving something from the latest stable version

For the versions available, see the [tags on this repository](https://github.com/jusbrasil/tornado-auth-php/tags).

[//]: # (### Release)

[//]: # (TODO: Add steps to release a new version.)

## Built With

* [PHP `v7.3.8`](https://www.php.net/) - Hypertext Preprocessor
* [Composer `v1.9.0`](https://getcomposer.org/) - Dependency Manager for PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* Based on [tornado-auth-js](https://github.com/jusbrasil/tornado-auth-js)
