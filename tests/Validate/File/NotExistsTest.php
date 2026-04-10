<?php

// Call Zend_Validate_File_NotExistsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_NotExistsTest::main");
}


/**
 * NotExists testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_File_NotExistsTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $baseDir = __DIR__;
        $valuesExpected = array(
            array($baseDir, 'testsize.mo', true),
            array($baseDir . '/_files', 'testsize.mo', false)
        );

        $files = array(
            'name'        => 'testsize.mo',
            'type'        => 'text',
            'size'        => 200,
            'tmp_name'    => __DIR__ . '/_files/testsize.mo',
            'error'       => 0
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_NotExists($element[0]);
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1]),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1], $files),
                "Tested with " . var_export($element, 1)
                );
        }

        $valuesExpected = array(
            array($baseDir, 'testsize.mo', true, false),
            array($baseDir . '/_files', 'testsize.mo', false, false)
        );

        $files = array(
            'name'        => 'testsize.mo',
            'type'        => 'text',
            'size'        => 200,
            'tmp_name'    => __DIR__ . '/_files/testsize.mo',
            'error'       => 0,
            'destination' => __DIR__ . '/_files'
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_NotExists($element[0]);
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1]),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid($element[1], $files),
                "Tested with " . var_export($element, 1)
            );
        }

        $valuesExpected = array(
            array($baseDir, 'testsize.mo', false, false),
            array($baseDir . '/_files', 'testsize.mo', false, false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_NotExists();
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1]),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid($element[1], $files),
                "Tested with " . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getDirectory() returns expected value
     *
     * @return void
     */
    public function testGetDirectory(): void
    {
        $validator = new Zend_Validate_File_NotExists('C:/temp');
        $this->assertEquals('C:/temp', $validator->getDirectory());

        $validator = new Zend_Validate_File_NotExists(array('temp', 'dir', 'jpg'));
        $this->assertEquals('temp,dir,jpg', $validator->getDirectory());

        $validator = new Zend_Validate_File_NotExists(array('temp', 'dir', 'jpg'));
        $this->assertEquals(array('temp', 'dir', 'jpg'), $validator->getDirectory(true));
    }

    /**
     * Ensures that setDirectory() returns expected value
     *
     * @return void
     */
    public function testSetDirectory(): void
    {
        $validator = new Zend_Validate_File_NotExists('temp');
        $validator->setDirectory('gif');
        $this->assertEquals('gif', $validator->getDirectory());
        $this->assertEquals(array('gif'), $validator->getDirectory(true));

        $validator->setDirectory('jpg, temp');
        $this->assertEquals('jpg,temp', $validator->getDirectory());
        $this->assertEquals(array('jpg', 'temp'), $validator->getDirectory(true));

        $validator->setDirectory(array('zip', 'ti'));
        $this->assertEquals('zip,ti', $validator->getDirectory());
        $this->assertEquals(array('zip', 'ti'), $validator->getDirectory(true));
    }

    /**
     * Ensures that addDirectory() returns expected value
     *
     * @return void
     */
    public function testAddDirectory(): void
    {
        $validator = new Zend_Validate_File_NotExists('temp');
        $validator->addDirectory('gif');
        $this->assertEquals('temp,gif', $validator->getDirectory());
        $this->assertEquals(array('temp', 'gif'), $validator->getDirectory(true));

        $validator->addDirectory('jpg, to');
        $this->assertEquals('temp,gif,jpg,to', $validator->getDirectory());
        $this->assertEquals(array('temp', 'gif', 'jpg', 'to'), $validator->getDirectory(true));

        $validator->addDirectory(array('zip', 'ti'));
        $this->assertEquals('temp,gif,jpg,to,zip,ti', $validator->getDirectory());
        $this->assertEquals(array('temp', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getDirectory(true));

        $validator->addDirectory('');
        $this->assertEquals('temp,gif,jpg,to,zip,ti', $validator->getDirectory());
        $this->assertEquals(array('temp', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getDirectory(true));
    }
}

// Call Zend_Validate_File_NotExistsTest::main() if this source file is executed directly.
