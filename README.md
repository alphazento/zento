# Introduction
This is a package to provide a solution of modularity your Laravel project with Laravel framework.

It includes three sub-packages: **Kernel, ThemeManager, UrlRewriter**.
These three packages only **"Kernel"** is mandatory enabled, UrlRewriter and ThemeManager you can enable or disable it base on your project requirement.

#### Kernel Package
**Kernel** package extends package management feature to Laravel project by just configuring some key concepts such as **Provider, Middleware, Middleware Group, Command Line, Route, Theme package, Listener**.

It provides a folder **mypackages** as private package codebase, you can create your private package in this folder by using **artisan make:package**.

And this package also provides some useful features:
1) Config system which can connect to DB(or you can define your config extension)
2) Dynamic Attribute, Event and Sequence Listeners.

By running command to enable this package:
```
php artisan package:enable Zento/Kernel
```

#### ThemeManager
By running command to enable this package:
```
php artisan package:enable Zento/ThemeManager
```

#### UrlRewriter

UrlRewriter provides a URL rewrite management for Laravel. You can connect a static URL to your Laravel route.

When the package is enabled, it will create a new table 'url_rewrite_rules' where you can manage your url rewrites.

By running command to enable this package:
```
php artisan package:enable Zento/UrlRewriter
```

# Installation
Please install it via composer:

```shell
composer require alphazento/zento:dev-master
```

## I. Package Development
Alphazento/Zento extends Laravel Package Discover and also provides a new **"mypackage"** folder in project root path. This folder will also be discovered by **"package:discover"** and you can put your private code base in here.

### Package Discover
This package extends Laravel [Package Discovery](https://laravel.com/docs/5.6/packages#package-discovery) feature by adding "zento" section to "extra"->"laravel" section of your package's composer.json file. A classic Zento package you would like to config:

```json
"extra": {
    "branch-alias": {
        "dev-master": "1.0-dev"
        },
        "laravel": {
            "providers": [
                "Zento\\Kernel\\Providers\\KernelProvider"
            ],
            "aliases": {
                "PackageManager": "Zento\\Kernel\\Facades\\PackageManager",
                "EventsManager": "Zento\\Kernel\\Facades\\EventsManager",
                "Debugger": "Zento\\Kernel\\Facades\\Debugger"
            },
            "zento": {
                "Vendor_SamplePackage":{
                "providers": [
                ],
                "version" : "0.0.1",
                "commands" : [
                ],
                "middlewares" : {},
                "middlewaregroup": {
                    "GROUPNAME" : {
                        "main" : [
                        ],
                        "pre" : [
                        ],
                        "post" : [
                        ],
                    },
                },
                "listeners" : {
                    "EVENT-CLASS-NAME" : {
                        "10":"OBSERVER-CLASS-NAME"
                        }
                },
            }
        }
    }
},
```


### MyPackage Folder Structure
You can create your package by running command:
```
artisan make:package VendorName_PackageName
```
Then it will pre-create a folder in the path: **projectroot/mypackages/VendorName/PackageName**

```shell
/mypackages/{VendorName/{PackageName# tree
.
|-- Console
| `-- Commands //put your console command class files here
|
|-- Http
| |-- Controllers //put your Controllers class files here
| `-- Middleware //put your middleware class files here
|
|-- Model //put your Model class files here
|
|-- Providers //put your ServiceProvider class files here
| `-- Facades
|
|-- Services //put your real service class files here
|
|-- Events //Put event class and listener class here
| |-- Listeners
|
|-- database //database migration management here
| |-- 0.0.1 //version number
| |-- 01_create_sample_table1.php
| |-- 0.0.2 //version number
| |-- 01_create_sample_table2.php
|
|-- resources //everything about frontend,please put here
| |-- public
| | |-- css
| | |-- font
| | |-- images
| | `-- js
| `-- views //your views. Please use it by: view('VendorName.'), with your VendorName_PackageName prefix.
|
|-- composer.json //Config extra/laravel/zento setting here
|
`-- routes.php.example //Please change it as "routes.php" if you want to use routes.
```

## II. Usage
### 1 Command Lines
This package extends some command lines:
#### 1) make:package

#### 2) package:enable
```shell
artisan package:enable VendorName_PackageName
```
It will register the package to the system, so it's provider, middleware, middlewaregroup, command lines and event listeners will be registered, then you can use these resources.

If a Zento package is not registered, those resource(list above) will not able to be used. But of cause, you still can use it's classes.

#### 2) package:disable
Disable package.(but it's classes still can be used.
```shell
artisan package:disable VendorName_PackageName
```

#### 3) package:discover
This command line is provide from original Laravel, but we extend it to discover the packages that you created in **mypackages**.
And it also merge and cache configuration items in your package's **composer.json** file.
```shell
artisan package:discover
```
#### 4) listeners
Zento Kernel has extended original Laravel Event/Listener. Original Laravel Event's Listener doesnpt support control listener call Sequence, but many time your listener must be called by a special Sequence.
By running the command:
```shell
artisan listeners
```
It will list your package listening to events and these listeners calling Sequence.


### 2. Extends Features
#### 1). Dynamic Attribute
Zento Kernel package bring dynamic Attribute feature to Eloqument. You can easily extends attributes to an exist eloqument without change model's database table.

Dynamic Attribute has two types:
##### single
attribute only has a value
##### option
attribe has multiple option values.

##### Create a dynamic Attribute for a model
DanamicAttributeFactory::createRelationShipORM($modelClassName, $dynamicAttributeName, $optionArray, $isSingleOrOptions)

By calling this function, it will generate a dynamic Attribute table for the model.
DanamicAttributeFactory::createRelationShipORM(\namespace\class::class,
'attribute', ['char', 32], true);

##### Extend withDynamicSingleAttribute and withDynamicOptionAttribute to retrieve dynamic attribute
You can use withDynamicSingleAttribute(single), or withDynamicOptionAttribute(option)
$collection = \Zento\Kernel\TestModel::where('id', 1)->withDynamicSingleAttribute('new_column')->first();
##### listDynamicAttributes
This function will list all dynamic attributes for an exists model

##### How to use it
If you want your Eloquemnt Model has ability of dynamic Attributes, you may do so using the Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\DynamicAttributeAbility trait. This trait is imported by default on hasOneDyn, hasManyDyns functions and they work with Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Builder to provide withDynamicSingleAttribute and withDynamicOptionAttribute:

```php
class TestModel extends \Illuminate\Database\Eloquent\Model {
use \Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\DynamicAttributeAbility;
}

DanamicAttributeFactory::createRelationShipORM(TestModel::class,
'new_column', ['char', 32], true);
DanamicAttributeFactory::createRelationShipORM(TestModel::class,
'new_column1', ['char', 32], false);

TestModel::listDynamicAttributes(); //list all dynamic Attributes

$collection = TestModel::where('id', 1)->withDynamicSingleAttribute('new_column')->withDynamicOptionAttribute('new_column1')->first();
```

#### 2). Config extends
Zento\Kernel package extends the original Laravel config feature. The original config only load config from config's folder. With this extends, we create a "config_items" table in the database, and "Zento\Kernel\Booster\Config\ConfigInDB\ConfigRepository" as the default database config repository.

When try to get a config value, it will try to use default config(which get from config folder), if there's not configuration item then it will try to get from the table.

It also provides an interface for you to customize the config repository. That means you can define your own config logic instead of the default one which store in the database.

You can define yourself config repository in config/zento.php
```php
'config_extend' => [
'extra_repository' => \Zento\Kernel\Booster\Config\ConfigInDB\ConfigRepository::class,
'grouping_provider' => null //config can be different by grouping
]
```
**extra_repository** is your own config repository
**grouping_provider** by providing this you will be able support different segment config groups.

#### 3). Event And Sequence Listener

Laravel provides a very good event and listener system. But for an event it's listeners are called base on when the listener is registered to the system. If a package are registered early and the listener maybe called earlier.

alphazento/zento extends Laravel Event and Listener, you just need to configure your listener in your composer.json, and each listener with it's sequence defined, then these listener will be called by your defined sequence.

If a package defined a listener for an event "EVENT-CLASS-NAME":
```json
"listeners" : {
        "EVENT-CLASS-NAME" : {
            "10":"OBSERVER-CLASS-NAME1"
            }
    },
```
And another package defined a listener for the same event "EVENT-CLASS-NAME":
```json
"listeners" : {
        "EVENT-CLASS-NAME" : {
            "5":"OBSERVER-CLASS-NAME2"
            }
    },
```

Then the call sequence will be:
OBSERVER-CLASS-NAME2, OBSERVER-CLASS-NAME1

##### Base Event Class and Base Listener Class
Zento\Kernel\Booster\Events\BaseEvent
Zento\Kernel\Booster\Events\BaseListener

Please extends these base classes that we will gather the event will be handled by which listeners and their call sequence details.