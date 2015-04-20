<?php

use HealthCheck\Service\FilePregMatcher;
use HealthCheck\Service\EnvironmentVariablesChecker;
use Zend\ServiceManager\ServiceLocatorInterface;

return array(
    'factories' => array(
        'EnvironmentVariablesChecker' => function (ServiceLocatorInterface $serviceLocator) {

            /** @var array $config */
            $config = $serviceLocator->get('Config');

            /** @var FilePregMatcher $filePregMatcher */
            $filePregMatcher = $serviceLocator->get('FilePregMatcher');

            return new EnvironmentVariablesChecker(
                $filePregMatcher,
                $config['health_check']['env_vars_markdown_uri']
            );
        },
    ),
    'services' => array(
        'FilePregMatcher' => new FilePregMatcher(),
    ),
    'shared' => array(
        'FilePregMatcher' => false,
    ),
);
