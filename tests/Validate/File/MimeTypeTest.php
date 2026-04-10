<?php

// Call Zend_Validate_File_MimeTypeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_MimeTypeTest::main");
}


/**
 * MimeType testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_File_MimeTypeTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array(array('image/jpg', 'image/jpeg'), true),
            array('image', true),
            array('test/notype', false),
            array('image/gif, image/jpg, image/jpeg', true),
            array(array('image/vasa', 'image/jpg', 'image/jpeg'), true),
            array(array('image/jpg', 'image/jpeg', 'gif'), true),
            array(array('image/gif', 'gif'), false),
            array('image/jp', false),
            array('image/jpg2000', false),
            array('image/jpeg2000', false),
        );

        $filetest = __DIR__ . '/_files/picture.jpg';
        $files = array(
            'name'     => 'picture.jpg',
            'type'     => 'image/jpg',
            'size'     => 200,
            'tmp_name' => $filetest,
            'error'    => 0
        );

        foreach ($valuesExpected as $element) {
            $options   = array_shift($element);
            $expected  = array_shift($element);
            $validator = new Zend_Validate_File_MimeType($options);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $expected,
                $validator->isValid($filetest, $files),
                "Test expected " . var_export($expected, 1) . " with " . var_export($options, 1)
                . "\nMessages: " . var_export($validator->getMessages(), 1)
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
        $validator = new Zend_Validate_File_MimeType('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new Zend_Validate_File_MimeType(array('image/gif', 'video', 'text/test'));
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new Zend_Validate_File_MimeType(array('image/gif', 'video', 'text/test'));
        $this->assertEquals(array('image/gif', 'video', 'text/test'), $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value
     *
     * @return void
     */
    public function testSetMimeType(): void
    {
        $validator = new Zend_Validate_File_MimeType('image/gif');
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
        $validator = new Zend_Validate_File_MimeType('image/gif');
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

    public function testSetAndGetMagicFile(): void
    {
        $validator = new Zend_Validate_File_MimeType('image/gif');
        if (!empty($_ENV['MAGIC'])) {
            $mimetype  = $validator->getMagicFile();
            $this->assertEquals($_ENV['MAGIC'], $mimetype);
        }

        try {
            $validator->setMagicFile('/unknown/magic/file');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('can not be', $e->getMessage());
        }
    }

    public function testSetMagicFileWithinConstructor(): void
    {
        $this->expectException(Zend_Validate_Exception::class);
        $validator = new Zend_Validate_File_MimeType(array('image/gif', 'magicfile' => __FILE__));
        $this->fail('Zend_Validate_File_MimeType should not accept invalid magic file.');
    }

    public function testOptionsAtConstructor(): void
    {
        $validator = new Zend_Validate_File_MimeType(array(
            'image/gif',
            'image/jpg',
            'headerCheck' => true));

        $this->assertTrue($validator->getHeaderCheck());
        $this->assertEquals('image/gif,image/jpg', $validator->getMimeType());
    }

    /**
     * @group ZF-9686
     */
    public function testDualValidation(): void
    {
        $valuesExpected = array(
            array('image', true),
        );

        $filetest = __DIR__ . '/_files/picture.jpg';
        $files = array(
            'name'     => 'picture.jpg',
            'type'     => 'image/jpg',
            'size'     => 200,
            'tmp_name' => $filetest,
            'error'    => 0
        );

        foreach ($valuesExpected as $element) {
            $options   = array_shift($element);
            $expected  = array_shift($element);
            $validator = new Zend_Validate_File_MimeType($options);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $expected,
                $validator->isValid($filetest, $files),
                "Test expected " . var_export($expected, 1) . " with " . var_export($options, 1)
                . "\nMessages: " . var_export($validator->getMessages(), 1)
            );

            $validator = new Zend_Validate_File_MimeType($options);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $expected,
                $validator->isValid($filetest, $files),
                "Test expected " . var_export($expected, 1) . " with " . var_export($options, 1)
                . "\nMessages: " . var_export($validator->getMessages(), 1)
            );
        }
    }

    /**
     * @group ZF-11784
     */
    public function testTryCommonMagicFilesFlag(): void
    {
        $validator = new Zend_Validate_File_MimeType('image/jpeg');
        $this->assertTrue($validator->shouldTryCommonMagicFiles());

        $validator->setTryCommonMagicFilesFlag(false);
        $this->assertFalse($validator->shouldTryCommonMagicFiles());

        $validator->setTryCommonMagicFilesFlag(true);
        $this->assertTrue($validator->shouldTryCommonMagicFiles());
    }

    /**
     * @group ZF-11784
     */
    public function testDisablingTryCommonMagicFilesIgnoresCommonLocations(): void
    {
        $filetest = __DIR__ . '/_files/picture.jpg';
        $files = array(
            'name'     => 'picture.jpg',
            'size'     => 200,
            'tmp_name' => $filetest,
            'error'    => 0
        );

        $validator = new Zend_Validate_File_MimeType(array('image/jpeg', 'image/jpeg; charset=binary'));

        $goodEnvironment = $validator->isValid($filetest, $files);

        if ($goodEnvironment) {
            /**
             * The tester's environment has magic files that are properly read by PHP
             * This prevents the test from being relevant in the environment
             */
            $this->markTestSkipped('This test environment works as expected with the common magic files, preventing this from being testable.');
        } else {
            // The common magic files detected the image as application/octet-stream -- try the PHP default
            // Note that if this  branch of code is entered then testBasic, testDualValidation,
            // as well as Zend_Validate_File_IsCompressedTest::testBasic and Zend_Validate_File_IsImageTest::testBasic
            // will be failing as well.
            $validator = new Zend_Validate_File_MimeType(array('image/jpeg', 'image/jpeg; charset=binary'));
            $validator->setTryCommonMagicFilesFlag(false);
            $this->assertTrue($validator->isValid($filetest, $files));
        }
    }
}

// Call Zend_Validate_File_MimeTypeTest::main() if this source file is executed directly.
