Modules management
------------------
## Module registration
To gain access to module this module should be enabled first. Module registration performed as console command as below:

```sh
$ php yii manager/register <moduleName[,moduleName]>
```

When module registratin in process the next actions will be performed:
* Module is added to the list of enabled modules.
* Module permissions that was defined are initialized and take effect (more on this [here](rbac.md)).

__Note__: `moduleName` can be an alias `all`, it is an alias to perform operation on any available module

## Module unregistration
If you do not need any of modules anymore you should consider to disable them to minimize load time and memory consumption.
Like the registration of module this action is a console command as below:

```sh
$ php yii manager/un-register <moduleName[,moduleName]>
```

__Note__: `moduleName` can be an alias `all`, it is an alias to perform operation on any available module

## Bootstrap
Module can have Bootstrap.php file that follows Yii 2 module bootstrap guide (should implement BootstrapInterface [more](http://www.yiiframework.com/doc-2.0/guide-structure-modules.html#bootstrapping-modules)).
If such file is present it will be used at application [Bootstrapping](http://www.yiiframework.com/doc-2.0/guide-runtime-bootstrapping.html) process.

## Module status
Module status 'enabled/disabled' can be acquired with the next console command:

```sh
$ php yii manager/status <moduleName[,moduleName]>
```

__Note__: `moduleName` can be an alias `all`, it is an alias to perform operation on any available module