<?php

// Call Zend_Validate_File_ExcludeExtensionTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_ExcludeExtensionTest::main");
}


/**
 * ExcludeExtension testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_File_ExcludeExtensionTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array('mo', false),
            array('gif', true),
            array(array('mo'), false),
            array(array('gif'), true),
            array(array('gif', 'pdf', 'mo', 'pict'), false),
            array(array('gif', 'gz', 'hint'), true),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ExcludeExtension($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileExcludeExtensionNotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileExcludeExtensionNotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
        $this->assertTrue(array_key_exists('fileExcludeExtensionFalse', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_ExcludeExtension('gif');
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
    }

    public function testCaseTesting(): void
    {
        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_ExcludeExtension(array('MO', 'case' => true));
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));

        $validator = new Zend_Validate_File_ExcludeExtension(array('MO', 'case' => false));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
    }

    /**
     * Ensures that getExtension() returns expected value
     *
     * @return void
     */
    public function testGetExtension(): void
    {
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(array('mo'), $validator->getExtension());

        $validator = new Zend_Validate_File_ExcludeExtension(array('mo', 'gif', 'jpg'));
        $this->assertEquals(array('mo', 'gif', 'jpg'), $validator->getExtension());
    }

    /**
     * Ensures that setExtension() returns expected value
     *
     * @return void
     */
    public function testSetExtension(): void
    {
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $validator->setExtension('gif');
        $this->assertEquals(array('gif'), $validator->getExtension());

        $validator->setExtension('jpg, mo');
        $this->assertEquals(array('jpg', 'mo'), $validator->getExtension());

        $validator->setExtension(array('zip', 'ti'));
        $this->assertEquals(array('zip', 'ti'), $validator->getExtension());
    }

    /**
     * Ensures that addExtension() returns expected value
     *
     * @return void
     */
    public function testAddExtension(): void
    {
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $validator->addExtension('gif');
        $this->assertEquals(array('mo', 'gif'), $validator->getExtension());

        $validator->addExtension('jpg, to');
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to'), $validator->getExtension());

        $validator->addExtension(array('zip', 'ti'));
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getExtension());

        $validator->addExtension('');
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getExtension());
    }
}

// Call Zend_Validate_File_ExcludeExtensionTest::main() if this source file is executed directly.
