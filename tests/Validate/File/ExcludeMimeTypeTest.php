<?php

// Call Zend_Validate_File_ExcludeMimeTypeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_ExcludeMimeTypeTest::main");
}


/**
 * ExcludeMimeType testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_File_ExcludeMimeTypeTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array('image/gif', true),
            array('image/jpeg', false),
            array('image', false),
            array('test/notype', true),
            array('image/gif, image/jpeg', false),
            array(array('image/vasa', 'image/jpeg'), false),
            array(array('image/gif', 'jpeg'), false),
            array(array('image/gif', 'gif'), true),
        );

        $filetest = __DIR__ . '/_files/picture.jpg';
        $files = array(
            'name'     => 'picture.jpg',
            'type'     => 'image/jpeg',
            'size'     => 200,
            'tmp_name' => $filetest,
            'error'    => 0
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ExcludeMimeType($element[0]);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $element[1],
                $validator->isValid($filetest, $files),
                "Tested with " . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getMimeType() returns expected value
     *
     * @return void
     */
    public function testGetMimeType(): void
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new Zend_Validate_File_ExcludeMimeType(array('image/gif', 'video', 'text/test'));
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new Zend_Validate_File_ExcludeMimeType(array('image/gif', 'video', 'text/test'));
        $this->assertEquals(array('image/gif', 'video', 'text/test'), $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value
     *
     * @return void
     */
    public function testSetMimeType(): void
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $validator->setMimeType('image/jpeg');
        $this->assertEquals('image/jpeg', $validator->getMimeType());
        $this->assertEquals(array('image/jpeg'), $validator->getMimeType(true));

        $validator->setMimeType('image/gif, text/test');
        $this->assertEquals('image/gif,text/test', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text/test'), $validator->getMimeType(true));

        $validator->setMimeType(array('video/mpeg', 'gif'));
        $this->assertEquals('video/mpeg,gif', $validator->getMimeType());
        $this->assertEquals(array('video/mpeg', 'gif'), $validator->getMimeType(true));
    }

    /**
     * Ensures that addMimeType() returns expected value
     *
     * @return void
     */
    public function testAddMimeType(): void
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $validator->addMimeType('text');
        $this->assertEquals('image/gif,text', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text'), $validator->getMimeType(true));

        $validator->addMimeType('jpg, to');
        $this->assertEquals('image/gif,text,jpg,to', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to'), $validator->getMimeType(true));

        $validator->addMimeType(array('zip', 'ti'));
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to', 'zip', 'ti'), $validator->getMimeType(true));

        $validator->addMimeType('');
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to', 'zip', 'ti'), $validator->getMimeType(true));
    }


    /**
     * Ensure validator is not affected by PHP bug #63976
     */
    public function testShouldHaveProperErrorMessageOnNotReadableFile(): void
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/jpeg');

        $this->assertFalse($validator->isValid('notexisting'));
        $this->assertEquals(
            array('fileExcludeMimeTypeNotReadable' => "File 'notexisting' is not readable or does not exist"),
            $validator->getMessages()
        );
    }
}

// Call Zend_Validate_File_ExcludeMimeTypeTest::main() if this source file is executed directly.
