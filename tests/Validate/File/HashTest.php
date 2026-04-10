<?php

// Call Zend_Validate_File_HashTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_HashTest::main");
}


/**
 * Hash testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_File_HashTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array('3f8d07e2', true),
            array('9f8d07e2', false),
            array(array('9f8d07e2', '3f8d07e2'), true),
            array(array('9f8d07e2', '7f8d07e2'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Hash($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $valuesExpected = array(
            array(array('ed74c22109fe9f110579f77b053b8bc3', 'algorithm' => 'md5'), true),
            array(array('4d74c22109fe9f110579f77b053b8bc3', 'algorithm' => 'md5'), false),
            array(array('4d74c22109fe9f110579f77b053b8bc3', 'ed74c22109fe9f110579f77b053b8bc3', 'algorithm' => 'md5'), true),
            array(array('1d74c22109fe9f110579f77b053b8bc3', '4d74c22109fe9f110579f77b053b8bc3', 'algorithm' => 'md5'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Hash($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_Hash('3f8d07e2');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileHashNotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Hash('3f8d07e2');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileHashNotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Hash('3f8d07e2');
        $this->assertTrue($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Hash('9f8d07e2');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));
        $this->assertTrue(array_key_exists('fileHashDoesNotMatch', $validator->getMessages()));
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash(): void
    {
        $validator = new Zend_Validate_File_Hash('12345');
        $this->assertEquals(array('12345' => 'crc32'), $validator->getHash());

        $validator = new Zend_Validate_File_Hash(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'crc32', '12333' => 'crc32', '12344' => 'crc32'), $validator->getHash());
    }

    /**
     * Ensures that setHash() returns expected value
     *
     * @return void
     */
    public function testSetHash(): void
    {
        $validator = new Zend_Validate_File_Hash('12345');
        $validator->setHash('12333');
        $this->assertEquals(array('12333' => 'crc32'), $validator->getHash());

        $validator->setHash(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'crc32', '12121' => 'crc32'), $validator->getHash());
    }

    /**
     * Ensures that addHash() returns expected value
     *
     * @return void
     */
    public function testAddHash(): void
    {
        $validator = new Zend_Validate_File_Hash('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32'), $validator->getHash());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32', '12321' => 'crc32', '12121' => 'crc32'), $validator->getHash());
    }
}

// Call Zend_Validate_File_HashTest::main() if this source file is executed directly.
