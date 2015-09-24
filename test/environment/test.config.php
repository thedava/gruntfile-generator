<?php

return array(
    Gruntfile::CONFIG_IMPORTS => array(
        'import1',
        'import2',
        uniqid('import'),
    ),
    Gruntfile::CONFIG_TASKS   => array(
        'testTask'   => array(
            'test:target',
            'test2:target1',
            'test2:target2',
        ),
        'randomTask' => [
            uniqid('random:_'),
        ]
    ),
    Gruntfile::CONFIG_TARGETS => array(
        'test'  => array(
            'target' => array(
                'do'     => 'something',
                'random' => mt_rand(),
            )
        ),
        'test2' => array(
            'target1' => array(
                'do'   => 'something too',
                'time' => time(),
            ),
            'target2' => array(
                'do'     => 'even more',
                'unique' => uniqid(),
            ),
        )
    ),
);