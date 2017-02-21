# Yii2 Domain

Domain Driven Design patters implementation for Yii2.

Includes realization of a following patters:
- Repository
- Entity
- Specification (very rough implementation through ActiveQuery)
- Strategy

## Package information

Latest Stable Version | Total downloads | Monthly Downloads | Licensing 
--------------------- |  -------------- | ----------------  | --------- 
[![Latest Stable Version](https://poser.pugx.org/dekeysoft/yii2-domain/v/stable)](https://packagist.org/packages/dekeysoft/yii2-domain) | [![Total Downloads](https://poser.pugx.org/dekeysoft/yii2-domain/downloads)](https://packagist.org/packages/dekeysoft/yii2-domain) | [![Monthly Downloads](https://poser.pugx.org/dekeysoft/yii2-domain/d/monthly)](https://packagist.org/packages/dekeysoft/yii2-domain) | [![License](https://poser.pugx.org/dekeysoft/yii2-domain/license)](https://packagist.org/packages/dekeysoft/yii2-domain)

## Requirements

**`PHP >= 5.6.0` is required.**

## Getting Started

Run the following command to add Yii2 Domain to your project's `composer.json`. See [Packagist](https://packagist.org/packages/dekeysoft/yii2-domain) for specific versions.

```bash
composer require dekeysoft/yii2-domain
```

Or you can copy this library from:
- [Packagist](https://packagist.org/packages/dekeysoft/yii2-domain)
- [Github](https://github.com/dekeysoft/yii2-domain)

For additional information and guides go to the [project documentation](docs/README.md)

## Overview

Goal of this library is to introduce Domain Driven Design(DDD) principles to Yii2 projects and to fix [ActiveRecord problem](http://www.mehdi-khalili.com/orm-anti-patterns-part-1-active-record/) 
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
this library is designed to solve [ActiveRecord problem](http://www.mehdi-khalili.com/orm-anti-patterns-part-1-active-record/) of Yii 2. Don't use this library if you are starting a new project and looking for 
a solution that would allow you to build a decent architecture - you need a good framework that would allow you do build high-quality solution and Yii 2 is not
a framework that would allow you to build high quality architecture and implement rich domain layer. 

"Yii2Domain" library is a crutch designed to solve issues of domain layer caused by ActiveRecord in existing projects.
There are few decent solutions to build domain layer in a new project:
- [Spot ORM](http://phpdatamapper.com/)
- [Symphony + Doctrine](http://symfony.com/doc/current/doctrine.html) (preferable solution)
- [Eloquent ORM](http://laravel.su/docs/5.2/eloquent) (if you like Laravel and you are not working with enterprise applications)

## Build status

CI status    | Code quality
------------ | ------------
[![Build Status](https://travis-ci.org/dekeysoft/yii2-domain.svg?branch=master)](https://travis-ci.org/dekeysoft/yii2-domain) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dekeysoft/yii2-domain/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dekeysoft/yii2-domain/?branch=master)