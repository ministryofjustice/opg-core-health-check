<?php

namespace HealthCheck\Service;

/**
 * Class FilePregMatcher
 *
 * Check multiple regular expressions (preg_match calls) on each line of a specified file with only one parse.
 * Memory efficient - parses and preg_matches the file one line at a time, not load the whole file contents into memory
 *
 * Fluent Example:
 * ===============
 *
 * $serviceLocator->get('filePregMatcher')
 *                ->givenFile('my/file.txt')
 *                ->pregMatch('/^[0-9]/', function($match) {
 *
 *                  })
 *                ->pregMatch('/^[A-Z]/', function($match) {
 *
 *                  })
 *                ->start();
 *
 * Non Fluent Example:
 * ===================
 *
 * $serviceLocator->get('filePregMatcher')
 *                ->pregMatch('/^[0-9]/', function($match) { }, 'my/file.txt');
 *
 * @package Application\Model\Service
 */
class FilePregMatcher
{
    /** @var string */
    private $filename;

    /** @var array[ array[ @var string, @var callable ] ] */
    private $regexCallbacks = array();

    /**
     * Fluent way to set/reset filename
     * for multiple pregMatches
     * @param string $filename
     * @return FilePregMatcher
     */
    public function givenFile($filename)
    {
        $this->filename = $filename;
        $this->regexCallbacks = array();

        return $this;
    }

    /**
     * Fluently sets a callback for a specified regex
     * @param string $regex
     * @param callable $matchCallback
     * @param string $filename - non fluent way to parse a file with single regex
     * @return FilePregMatcher
     */
    public function pregMatch($regex, callable $matchCallback, $filename = null)
    {
        if ($filename === null) {
            $this->regexCallbacks[] = array($regex, $matchCallback);
            return $this;
        }

        $this->givenFile($filename)
            ->pregMatch($regex, $matchCallback)
            ->start();
    }

    /**
     * Loops over each line in the given file and fires the callback for each specified regex when matched
     */
    public function start()
    {
        $this->checkFluency();

        $file = new \SplFileObject($this->filename);

        while (!$file->eof()) {
            $line = $file->fgets();

            foreach ($this->regexCallbacks as $regex) {
                preg_match($regex[0], $line, $match);

                if (!empty($match)) {
                    call_user_func($regex[1], $match);
                }
            }
        }

        $file = null; //closes the file
    }

    /**
     * @throws \LogicException
     */
    private function checkFluency()
    {
        if (!isset($this->filename)) {
            throw new \LogicException('Filename not set, call $this->givenFile() first');
        }

        if (empty($this->regexCallbacks)) {
            throw new \LogicException('No regex callbacks set, call $this->pregMatch($regex, $matchCallback) first');
        }
    }
}
