<?php

return array(
    Gruntfile::CONFIG_IMPORTS => array(
        'import1',
        'import2',
    ),
    Gruntfile::CONFIG_TASKS   => array(
        'testTask'   => array(
            'test1:target',
            'test2:target1',
            'test2:target2',
        ),
    ),
    Gruntfile::CONFIG_TARGETS => array(
        'test1'  => array(
            'target' => array(
                'do'     => 'something',
            )
        ),
        'test2' => array(
            'target1' => array(
                'do'   => 'something too',
            ),
            'target2' => array(
                'do'     => 'even more',
            ),
        )
    ),
);