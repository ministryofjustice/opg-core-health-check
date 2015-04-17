<?php

namespace HealthCheckTest\Service;

use HealthCheck\Service\EnvironmentVariablesChecker;
use HealthCheck\Service\FilePregMatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;

class EnvironmentVariablesCheckerTest extends AbstractControllerTestCase
{
    /** @var EnvironmentVariablesChecker */
    private $checker;

    public function setUp()
    {
        $this->checker = new EnvironmentVariablesChecker(
            new FilePregMatcher(),
            __DIR__ . '/../fixtures/EnvironmentVariablesCheckerTest.md'
        );
    }

    public function testMissingAllHealthCheckEnvVars()
    {
        try {
            $this->checker->check();
            $this->fail('Expected HttpException not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
            $this->assertEquals(
                'Environment variable(s) missing: HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_1, ' .
                'HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_2, HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_3',
                $e->getMessage()
            );
        }

    }

    public function testMissingOneHealthCheckEnvVar()
    {
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_1=foo');
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_2=bar');

        try {
            $this->checker->check();
            $this->fail('Expected HttpException not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
            $this->assertEquals(
                'Environment variable(s) missing: HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_3',
                $e->getMessage()
            );
        }
    }

    public function testHealthCheckEnvVarsAreSetCausesNoException()
    {
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_1=foo');
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_2=bar');
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_3=baz');

        $this->checker->check();
    }

    public function tearDown()
    {
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_1');
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_2');
        putenv('HEALTH_CHECK_REST_CONTROLLER_TEST_ENV_3');
    }
}
