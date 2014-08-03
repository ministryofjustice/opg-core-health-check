<?php

namespace Application;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDiagnostics\Check\ClassExists;
use ZendDiagnostics\Check\ExtensionLoaded;
use ZendDiagnostics\Check\HttpService;
use ZendDiagnostics\Check\ProcessRunning;

class Module implements ConsoleUsageProviderInterface, ServiceLocatorAwareInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $sl;

    /**
     * @param ServiceLocatorInterface $sl
     */
    public function setServiceLocator( ServiceLocatorInterface $sl )
    {
        $this->sl = $sl;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->sl;
    }

    public function onBootstrap( MvcEvent $e )
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach( $eventManager );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConsoleUsage( Console $console )
    {
        return array(
            'php public/index.php dia -v'
        );
    }

    /**
     * This method should return an array of checks,
     */
    public function getDiagnostics()
    {
        return array(
            'Ngnix is running'         => function () {
                return ( new ProcessRunning( 'ngnix' ) )->check();
            },
            'Postgres is running'      => function () {
                return ( new ProcessRunning( 'postgres' ) )->check();
            },
            'ElasticSearch is running' => function () {
                return ( new ProcessRunning( 'elasticsearch' ) )->check();
            },
            'Modules are available'    => function () {
                return (
                new ExtensionLoaded(
                    array(
                        'PDO',
                        'xhprof',
                        'xdebug',
                        'calendar',
                        'gd',
                    )
                )
                )->check();
            },
            'Development mode'         => function () {
                return getenv( 'APP_ENV' ) == 'development';
            },
            'Cache directory exists'   => function () {
                return file_exists( 'data/cache' ) && is_dir( 'data/cache' );
            },
            'Frontend is running'      => function () {
                $check = new HttpService( 'front.local', 80, '/auth', 200 );
                return $check->check();
            },
            'Membrane is running'      => function () {
                $check = new HttpService( 'membrane.local', 80, '/', 200 );
                return $check->check();
            },
            'Auth Service is running'  => function () {
                $check = new HttpService( 'membrane.local', 80, '/auth/sessions', 200 );
                return $check->check();
            },
            'Backend is running'       => function () {
                $check = new HttpService( 'api.local', 80, '/api/heartbeat', 200 );
                return $check->check();
            },
            // check data in the database
            // check data in elastic
        );
    }
}
