<?php

namespace HealthCheck\Factory;

use HealthCheck\Service\EnvironmentVariablesChecker;
use HealthCheck\Service\FilePregMatcher;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class EnvironmentVariablesCheckerFactory
 * Factory used to create EnvironmentVariablesChecker instance
 * @package HealthCheck\Factory
 */
class EnvironmentVariablesCheckerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return EnvironmentVariablesChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var array $config */
        $config = $serviceLocator->get('Config');

        /** @var FilePregMatcher $filePregMatcher */
        $filePregMatcher = $serviceLocator->get('FilePregMatcher');

        return new EnvironmentVariablesChecker(
            $filePregMatcher,
            $config['health_check']['env_vars_markdown_uri']
        );
    }
}
