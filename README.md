# gruntfile-generator
A PHP based Gruntfile.js generator

[![Build Status](https://travis-ci.org/thedava/gruntfile-generator.svg?branch=master)](https://travis-ci.org/thedava/gruntfile-generator)


## Installation
    php composer.phar require thedava/gruntfile-generator


## Console command
	php bin/gruntfile --config=your_grunt_config.php --gruntfile=Gruntfile.js


## A basic configuration file
```php
<?php

return array(
    Gruntfile::CONFIG_IMPORTS => array(
        'your-grunt-import'
    ),
    Gruntfile::CONFIG_TASKS => array(
        'your_task' => array(
            'some:target'
        )
    ),
    Gruntfile::CONFIG_TARGETS => array(
        'some' => array(
            'target' => array(
                'do' => 'something'
            )
        )
    )
);
```


## Gruntfile generation
Add the gruntfile-generator as target to your grunt configuration for maximum efficiency. All you need is [grunt-exec](https://github.com/jharding/grunt-exec) as import.

```php
<?php

return array(
    Gruntfile::CONFIG_IMPORTS => array(
        'grunt-exec'
    ),
    Gruntfile::CONFIG_TASKS => array(
        'gruntfile' => array(
            'exec:gruntfile'
        )
    ),
    Gruntfile::CONFIG_TARGETS => array(
        'exec' => array(
            'gruntfile' => 'php bin/gruntfile --config=this_file.php --gruntfile=Gruntfile.js'
        )
    )
);
```

Run your exec target once manually on the cli and your Gruntfile will be generated. Now you can use ```grunt gruntfile``` to update your gruntfile from config


## Extended config files

You can seperate your config into multiple files and build a valid gruntfile generator config using the Gruntfile class.

Let's assume that your structure looks like this:

	root
	|-- bin
	|    |-- gruntfile
	|
	|-- config
	|    |-- grunt.config.php
	|    |-- tasks
	|    |    |-- task1.config.php
	|    |    |-- task2.config.php
	|    |    |-- ...


Your grunt.config.php could look like this:

```php
<?php

$configFiles = glob(__DIR__.'/tasks/*.config.php');
return Gruntfile::mergeConfigs($configFiles);
```


Now you can add a new file for every task/target/thing you need to do. This keeps your config simple and clean. The gruntfile generator will build one single Gruntfile at the end.