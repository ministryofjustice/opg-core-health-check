<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'EnvironmentVariablesChecker' => 'HealthCheck\Factory\EnvironmentVariablesCheckerFactory',
            'FilePregMatcher' => function () {
                return new \HealthCheck\Service\FilePregMatcher();
            }
        ),
        'shared' => array(
            'FilePregMatcher' => false,
        ),
    )
);
