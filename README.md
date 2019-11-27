<p align="center">
    <img src="https://github.com/php-kitchen/yii2-domain/blob/master/docs/logo.png" width="600px">
</p>

<p align="center">
    <a href="https://app.buddy.works/php-kitchen/yii2-domain/pipelines"><img src="https://app.buddy.works/php-kitchen/yii2-domain/pipelines/pipeline/225818/badge.svg?token=b1a396bc03020a62450dcceeaf652de56c287593c5a899155bfcab4b65ce5641" alt="Build Status"></a>
    <a href="https://github.com/php-kitchen/code-specs"><img src="https://img.shields.io/badge/Tested_By-CodeSpecs-brightgreen.svg" alt="Tested By"></a>
    <a href="https://codeclimate.com/github/php-kitchen/yii2-domain/maintainability"><img src="https://api.codeclimate.com/v1/badges/0af02187488d0d2d70ad/maintainability" /></a>
    <a href="https://packagist.org/packages/php-kitchen/yii2-domain"><img src="https://poser.pugx.org/php-kitchen/yii2-domain/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/php-kitchen/yii2-domain"><img src="https://poser.pugx.org/php-kitchen/yii2-domain/d/monthly" alt="Monthly Downloads"></a>
    <a href="https://packagist.org/packages/php-kitchen/yii2-domain"><img src="https://poser.pugx.org/php-kitchen/yii2-domain/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/php-kitchen/yii2-domain"><img src="https://poser.pugx.org/php-kitchen/yii2-domain/license.svg" alt="License"></a>
</p>



**Yii2 Domain** is a Domain Driven Design patterns implementation for Yii2.

Includes realization of a following patterns:
- Repository
- Entity
- Specification (very rough implementation through ActiveQuery)
- Strategy

## Requirements

**`PHP >= 7.1` is required.**

## Getting Started

Run the following command to add Yii2 Domain to your project's `composer.json`. See [Packagist](https://packagist.org/packages/php-kitchen/yii2-domain) for specific versions.

```bash
composer require php-kitchen/yii2-domain
```

Or you can copy this library from:
- [Packagist](https://packagist.org/packages/php-kitchen/yii2-domain)
- [Github](https://github.com/php-kitchen/yii2-domain)

For additional information and guides go to the [project documentation](docs/README.md)

## Overview

Goal of this library is to introduce Domain Driven Design(DDD) principles to Yii2 projects and to fix [ActiveRecord problem](http://www.mehdi-khalili.com/orm-anti-patterns-part-1-active-record) 
 of domain layer in applications with medium and large domain area.

Each model represented as a standalone directory that contains repository, entity, record and query classes. All of these 
classes represent a domain model.

DIRECTORY STRUCTURE OF A TYPICAL MODEL(AS EXAMPLE - USER MODEL)
-------------------
      user/                    contains all of the classes that represents domain model
            UserRepository     model repository
            UserEntity         model entity(represents domain entity - not the DB table that containd entity information) 
            UserRecord         DB record that contains entity information
            ProfileRecord      DB record with additional information thta also a part of the UserEntity.
            UserQuery          query class of model e.g. - specification of the entity
 

## Code examples

Simple search and store:
```php

$repository = new UserRepository();
$entity = $repository->findOneWithPk(1);
// do some manipulations with entity
$repository->validateAndSave($entity);
```

Complex criteria search and deleting:
```php

$repository = new UserRepository();
$entity = $repository->find()
		->active()
		->withoutEmail()
		->one();
// do some manipulations with entity
$repository->delete($entity);
```

## Note:
this library is designed to solve [ActiveRecord problem](http://www.mehdi-khalili.com/orm-anti-patterns-part-1-active-record) of Yii 2. Don't use this library if you are starting a new project and looking for 
a solution that would allow you to build a decent architecture - you need a good framework that would allow you do build high-quality solution and Yii 2 is not
a framework that would allow you to build high quality architecture and implement rich domain layer. 

"Yii2Domain" library is a crutch designed to solve issues of domain layer caused by ActiveRecord in existing projects.
There are few decent solutions to build domain layer in a new project:
- [Spot ORM](http://phpdatamapper.com/)
- [Symfony + Doctrine](http://symfony.com/doc/current/doctrine.html) (preferable solution)
- [Eloquent ORM](http://laravel.su/docs/5.4/eloquent) (if you like Laravel and you are not working with enterprise applications)

## Contributing

If you want to ask any questions, suggest improvements or just to talk with community and developers, [join our server at Discord](https://discord.gg/Ez5VZhC) 


