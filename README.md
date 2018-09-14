## Introduction
This is a package to provide a solution of modularity your Laravel project with Laravel framework.

It help you to extend your package into your Laravel project by just configurate some key concepts such as **Provider, Middleware, Middleware Group, Command Line, Route, Theme package, Listener**. And it also create a new folder as **mypackages**, you can create your private package in this folder by using **artisan make:package**.

### I. Package Development
#### Package Discover Add Automount
It extends Laravel [Package Discovery](https://laravel.com/docs/5.6/packages#package-discovery) feature by adding "zento" section to "extra"->"laravel" section of your package's composer.json file. A classic Zento package you would like to config:

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
                    "middlewares" : {},
                    "middlewaregroup": [
                        "GROUPNAME" : {
                            "main" : [
                            ],
                            "pre" : [
                            ],
                            "post" : [
                            ],
                        },
                    ],
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


#### MyPackage Folder Structure
As Memtioned before you can create your package by running command:
```
    artisan make:package VendorName_PackageName
```
Then it will pre-create a folder in the path: **projectroot/mypackages/VendorName/PackageName**

```shell
/mypackages/{VendorName/{PackageName# tree
.
|-- Console
|   `-- Commands                     //put your console command class files here
|
|-- Http
|   |-- Controllers                  //put your Controllers class files here
|   `-- Middleware                   //put your middleware class files here
|
|-- Model                            //put your Model class files here
|
|-- Providers                        //put your ServiceProvider class files here
|   `-- Facades
|
|-- Services                         //put your real service class files here
|
|-- Events                          //Put event class and listener class here
|   |-- Listeners                   
|
|-- database                         //database migration management here
|   |-- 0.0.1                        //version number
|       |-- 01_create_sample_table1.php
|   |-- 0.0.2                        //version number
|       |-- 01_create_sample_table2.php
|
|-- resources                        //everything about frontend,please put here
|   |-- public
|   |   |-- css
|   |   |-- font
|   |   |-- images
|   |   `-- js
|   `-- views                        //your views. Please use it by: view('VendorName.'), with your VendorName_PackageName prefix.
|
|-- composer.json                   //Config extra/laravel/zento setting here
|
`-- routes.php.example               //Please change it as "routes.php" if you want to use routes.
```


### II. Usage 
#### 1 Command Lines
This package extends some command lines:
##### 1) package:enable       
```shell
    artisan package:enable VendorName_PackageName
```
It will register the package to the system, so it's provider, middleware, middlewaregroup, command lines and event listeners will be registered, then you can use these resources.

If a Zento package is not registered, those resource(list above) will not able to be used. But of cause you still can use it's classes.

##### 2) package:disable      
Disable package.(but it's classes still can be used.
```shell
    artisan package:disable VendorName_PackageName
```

##### 3) package:discover
This command line is provide from original Laravel, but we extend it to discover the packages that you created in **mypackages**. 
And it also merge and cache configuration items in your package's **composer.json** file.
```shell
    artisan package:discover
```
##### 4) listener:list
Zento Kernel has extended original Laravel Event/Listener. Original Laravel Event's Listener doesnpt support control listener call sequency, but many time your listener must be called by a special sequency.
By running command:
```shell
    artisan listener:list
```
It will list your package listening to events and these listeners calling sequency.


#### 2. Extends Features
##### 1). Dynamic Column
Zento Kernel package bring dynamic column feature to Eloqument. You can easily extends attributes to an exist eloqument without change model's database table.

Dynamic Column has two types:
###### single 
attribute only has a value
###### option
attribe has multiple option values.

###### Create a dynamic column for a model
DynaColumnFactory::createRelationShipORM($modelClassName, $dynamicColumnName, $optionArray, $isSingleOrOptions)

By calling this function, it will generate a dynamic column table for the model.
DynaColumnFactory::createRelationShipORM(\namespace\class::class, 
    'attribute', ['char', 32], true);

###### Extend withDyn and withDyns to retrieve dynamic columns
    You can use withDyn(single), or withDyns(option)
    
    $collection = \Zento\Kernel\TestModel::where('id', 1)->withDyn('new_column')->first();
###### listDynaColumns
    This function will list all dynamic columns for an exists model

###### Example
    by addting trait
        
#### 2. config extends
This package also extends original Laravel config feature. The original config only load config from config's folder. With this extends, we create a table in database.

So when you try to get a config value, it will try to use default config(which get from config folder), if there's not configuration item and it will try to get from database.

It also provider interface for you to change config engine. That means you can define your own config logic instead of the default one which store in database.

## Log extends

## event extends