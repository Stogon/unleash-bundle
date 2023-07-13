# Contributing

## Code style

Your code must follow the standards defined in linters.

* [EditorConfig](./.editorconfig)
* [PHP CS-Fixer](./.php-cs-fixer.dist.php)

To ensure that your code match the defined standards, you can use the following command to automatically format everything for you :
```
composer run format
```

## Tests

Global testing is done using **Codecov**.

* [Codecov](https://codecov.io/gh/Stogon/unleash-bundle)

### PHP

PHP code is tested using the following libraries

* [PHPUnit](https://phpunit.readthedocs.io)
* [PHPStan](https://phpstan.org)

To make sure your code works well, you can use the following command :
```
composer run test
```
