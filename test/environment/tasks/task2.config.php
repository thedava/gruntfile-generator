<?php

return array(
    Gruntfile::CONFIG_IMPORTS => array(
        'import3',
        'import4',
    ),
    Gruntfile::CONFIG_TASKS   => array(
        'testTask2'   => array(
            'test3:target',
            'test4:target',
        )
    ),
    Gruntfile::CONFIG_TARGETS => array(
        'test3'  => array(
            'target' => array(
                'do'     => 'something',
            )
        ),
        'test4' => array(
            'target' => array(
                'do'     => 'something',
            )
        )
    ),
);