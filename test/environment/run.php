<?php

require_once __DIR__.'/../../vendor/autoload.php';

chdir(__DIR__);

echo shell_exec('php ../../gruntfile --config=test.config.php --gruntfile=Gruntfile.js');