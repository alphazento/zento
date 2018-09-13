## Introduction
This is a package to provide a solution of modularity your Laravel project with Laravel.

It can enable/disable your package dynamicly by changing your database setting.

It extends Laravel Package Discovery feature by adding "zento" section to "extra"->"laravel" section of your package's composer.json file.

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
                    "providers": [  //this providers will registered after Zento_Kernel's provider
                    ],
                    "version" : "0.0.1", //package version
                    "commands" : [
                    ],
                    "middlewares"=>[],
                    "middlewaregroup" => [
                        "GROUPNAME" => [
                            "replace" => [
                            ],
                            "prepend" => [
                            ],
                            "after" => [
                            ],
                        ],
                    ],
                    "listeners"=>[
                        "EVENT-CLASS-NAME" => [
                            10 => "OBSERVER-CLASS-NAME"
                        ]
                    ],
                }
            }
        }
    },
```

As a alphazento package, 


### Benefits 
#### 1. Use LaBooster you can easy develop and manage a module, and realy easy to reuse it in other project.

In your module, you only need to focus the module's service provider, middleware, middlewaregroup, and console commands.

And also you also can easy config the routes which you want to expose to the application from the module.

#### 2. Easy Database Migration management

Just put database Migration files into module's sub-folder 'database/{version}', and run command 'artisan module:up {modulename}',
and it will parse the database version and migrate database.

#### 3. Laravel commands 'make:*' extened.

When you try to make a model, and you type in "artisan make:model test", it will prompt availabe module list, to ask you attach it to the module.


### How to install
composer require

### Create a Laravel application

composer create-project --prefer-dist laravel/laravel laravelapp

### Config your project

#### module name
Module name is case sensitive and it composed by two parts:Organization and ModuleName.

And using underscore to join Organization and ModuleName.


##### 1. Create a folder for your modules.(eg. modules)

    laravelapp_base_path/modules


##### 2. Edit your Laravel project composer.json psr-4 node, add 
 It should looks like below:
        "psr-0": {
            "": "modules/"
        }

##### 3. Edit your Laravel project "config/app.php", add "Yong\LaBooster\Providers\Root::class" to providers

##### 4. if your moduels folder name is not modules, you should add 'modules_path' => 'modules', to config/app.php


#### Create a module
##### 1. create a new module by running command:
```shell
    artisan make:module Organization_ModuleName
```
    It will create sub folders in this path:

```shell
    laravelapp_base_path/modules/Organization/ModuleName
```


##### 2. Package folders structure
    Now you may find under the folder laravelapp_base_path/modules/Organization/ModuleName we've pre-created many folders and files.
    You can edit these folders and files to develop your module.
```shell
root@a89b06c30e0a:/var/www/html/modules/Organization/ModuleName# tree
.
|-- Console
|   `-- Commands                     //put your console command class files here
|-- Http
|   |-- Controllers                  //put your Controllers class files here
|   `-- Middleware                   //put your middleware class files here
|-- Model                            //put your Model class files here
|-- Providers                        //put your ServiceProvider class files here
|   `-- Facades
|-- Services                         //put your real service class files here
|-- config
|   `-- settings.php                 //Your module configs
|-- database                         //database migration management here
|-- resources                        //everything about frontend,please put here
|   |-- public
|   |   |-- css
|   |   |-- font
|   |   |-- images
|   |   `-- js
|   `-- views                        //your views. Please use it by: view('organization_module.'), with your module name organization_module prefix.
`-- routes.php.example               //Please change it as "routes.php" if you want to use routes.

```

##### 3. Create database migration file
    Go to database folder, and make a subfolder '0.0.1'(which is the module's new version).
    And create a file with name "01_create_test_table.php".

##### 4. Edit module config
    Module config file is located in config/settings.php
    ***Please do not change the location and the file name***
    And the setting.php is inited from template. It should looks like:

```php
    <?php
return [
    'version'=>'0.0.1',
    'module'=>[
        'theme'=> false, //true,false,or name of inheritance's theme module
        'providers'=>[],
        'middlewares'=>[],
        'middlewaregroup' => [
            'groupname' => [
                'replace' => [
                    //if has replace, it will ignore prepend and after
                ],
                'prepend' => [
                ],
                'after' => [
                ],
              ],
        ],
        'commands'=>[],
        'aliases'=>[],
        ],
    ];
```
    
    Based on your module's details, fill the array.

##### 5. Define the routes.php to add your routes

##### 6. Implements models, controllers and middlewares

##### 7. Define view and use view
    Go to folder resources/views, you can add your self view. For example, I add a folder pages and a view name home.blade.php
```shell
    resources/views/pages/home.blade.php
```
    and when I use the view, just call it with module name prefix.
```php
    view('organization_module.pages.home');
```

* 7. Enable a module by running command:
```shell
    artisan module:up Organization_ModuleName
```


Now the module is ready for use.

## Installation

##### Install via composer
```shell
    composer require alphazento/zento
```


## Features
#### 1. Dynamic Column
    Zento Kernel package bring dynamic column feature to Eloqument. You can easily extends attributes to an exist eloqument without change model's database table.

    Dynamic Column has two type, single for attribute only has one value, option for attribe has multiple option values.

##### 1)  Create a dynamic column for a model
    DynaColumnFactory::createRelationShipORM($modelClassName, $dynamicColumnName, $optionArray, $isSingleOrOptions)

    By calling this function, it will generate a dynamic column table for the model.
    DynaColumnFactory::createRelationShipORM(\namespace\class::class, 
        'attribute', ['char', 32], true);

##### 2) Extend withDyn and withDyns to retrieve dynamic columns
    You can use withDyn(single), or withDyns(option)
    
    $collection = \Zento\Kernel\TestModel::where('id', 1)->withDyn('new_column')->first();
##### 3) listDynaColumns
    This function will list all dynamic columns for an exists model

##### 4) how to use
    by addting trait
        

#### 2. config extends

## Log extends

## event extends

## command lines

## package folder struct

##