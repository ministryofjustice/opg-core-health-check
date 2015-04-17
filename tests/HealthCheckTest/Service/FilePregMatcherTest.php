<?php

namespace HealthCheckTest\Service;

use HealthCheck\Service\FilePregMatcher;
use LogicException;

class FilePregMatcherTest extends \PHPUnit_Framework_TestCase
{
    private $filename;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockCallback;

    public function setUp()
    {
        $this->filename =  sys_get_temp_dir() . '/filePregMatcherTest.txt';

        $this->unlinkFile();

        file_put_contents(
            $this->filename,
            'Name: test, person' . PHP_EOL.
            'Email: test.person@address.com' . PHP_EOL.
            'Email: test.person@address2.com' . PHP_EOL.
            'DOB: 01/01/1970'
        );

        $this->mockCallback = $this->getMock(
            'stdClass',
            array('nameCallback', 'emailCallback')
        );
    }

    public function testNonFluentPregMatch()
    {
        $this->mockCallback->expects($this->once())
            ->method('nameCallback')
            ->with($this->equalTo(array('Name: test, person', 'test', 'person')));

        $this->mockCallback->expects($this->never())
            ->method('emailCallback')
            ->withAnyParameters();

        $matcher = new FilePregMatcher();
        $matcher->pregMatch(
            '/^Name: (.*), (.*)/',
            array($this->mockCallback, 'nameCallback'),
            $this->filename
        );

    }

    public function testFluentPregMatch()
    {
        $this->mockCallback->expects($this->at(0))
            ->method('nameCallback')
            ->with($this->equalTo(array('Name: test, person', 'test', 'person')));

        $this->mockCallback->expects($this->at(1))
            ->method('emailCallback')
            ->with($this->equalTo(array('Email: test.person@address.com', 'test.person', 'address.com')));

        $this->mockCallback->expects($this->at(2))
            ->method('emailCallback')
            ->with($this->equalTo(array('Email: test.person@address2.com', 'test.person', 'address2.com')));

        $this->mockCallback->expects($this->exactly(1))
            ->method('nameCallback');

        $this->mockCallback->expects($this->exactly(2))
            ->method('emailCallback');

        $matcher = new FilePregMatcher();
        $matcher->givenFile($this->filename)
            ->pregMatch('/^Name: (.*), (.*)/', array($this->mockCallback, 'nameCallback'))
            ->pregMatch('/^Email: (.*)@(.*)/', array($this->mockCallback, 'emailCallback'))
            ->start();
    }

    public function testGivenFileMethodResetsRegexCallbacks()
    {
        $this->mockCallback->expects($this->never())
            ->method('nameCallback')
            ->withAnyParameters();

        $this->mockCallback->expects($this->at(0))
            ->method('emailCallback')
            ->with($this->equalTo(array('Email: test.person@address.com', 'test.person', 'address.com')));

        $this->mockCallback->expects($this->at(1))
            ->method('emailCallback')
            ->with($this->equalTo(array('Email: test.person@address2.com', 'test.person', 'address2.com')));

        $this->mockCallback->expects($this->exactly(2))
            ->method('emailCallback');

        $matcher = new FilePregMatcher();
        $matcher->pregMatch('/^Name: (.*), (.*)/', array($this->mockCallback, 'nameCallback'))
            ->givenFile($this->filename)
            ->pregMatch('/^Email: (.*)@(.*)/', array($this->mockCallback, 'emailCallback'))
            ->start();
    }

    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage Filename not set, call $this->givenFile() first
     */
    public function testFilenameSetException()
    {
        $matcher = new FilePregMatcher();
        $matcher->start();
    }

    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage No regex callbacks set, call $this->pregMatch($regex, $matchCallback) first
     */
    public function testRegexCallbacksSetException()
    {
        $matcher = new FilePregMatcher();
        $matcher->givenFile($this->filename)
            ->start();
    }

    public function tearDown()
    {
        $this->unlinkFile();
    }

    private function unlinkFile()
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
