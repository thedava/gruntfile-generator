<?php

return array(
    Gruntfile::CONFIG_IMPORTS => array(
        uniqid('import'),
    ),
    Gruntfile::CONFIG_TASKS   => array(
        'randomTask' => [
            uniqid('random:_'),
        ]
    ),
    Gruntfile::CONFIG_TARGETS => array(
        'random'  => array(
            'target' => array(
                'random' => mt_rand(),
            )
        )
    ),
);