<?php

namespace HealthCheck\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnvironmentVariablesChecker
{
    /** @var FilePregMatcher */
    private $filePregMatcher;

    /** @var string */
    private $markdownUri;

    /**
     * @param FilePregMatcher $filePregMatcher
     * @param string $markdownUri
     */
    public function __construct(FilePregMatcher $filePregMatcher, $markdownUri)
    {
        $this->filePregMatcher = $filePregMatcher;
        $this->markdownUri = $markdownUri;
    }

    /**
     * @throws HttpException
     */
    public function check()
    {
        $missing = array();

        $this->filePregMatcher->pregMatch(
            "/^\\| ([A-Z_0-9]+) /",
            function ($match) use (&$missing) {
                $expectedEnvVar = $match[1];

                if (getenv($expectedEnvVar) === false) {
                    $missing[] = $expectedEnvVar;
                }
            },
            $this->markdownUri
        );

        if (count($missing) > 0) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                sprintf('Environment variable(s) missing: %s', implode(', ', $missing))
            );
        }
    }
}
