<?php

// Call Zend_Validate_File_FilesSizeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_FilesSizeTest::main");
}


use PHPUnit\Framework\TestCase;

class Zend_Validate_File_FilesSizeTest extends TestCase
{
    /**
     * @var bool
     */
    protected $multipleOptionsDetected;

    public function setUp(): void
    {
        $this->multipleOptionsDetected = false;
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array(array('min' => 0, 'max' => 2000), true, true, false),
            array(array('min' => 0, 'max' => '2 MB'), true, true, true),
            array(array('min' => 0, 'max' => '2MB'), true, true, true),
            array(array('min' => 0, 'max' => '2  MB'), true, true, true),
            array(2000, true, true, false),
            array(array('min' => 0, 'max' => 500), false, false, false),
            array(500, false, false, false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_FilesSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid(__DIR__ . '/_files/testsize2.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid(__DIR__ . '/_files/testsize3.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_FilesSize(array('min' => 0, 'max' => 200));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileFilesSizeNotReadable', $validator->getMessages()));

        $validator = new Zend_Validate_File_FilesSize(array('min' => 0, 'max' => 500000));
        $this->assertEquals(true, $validator->isValid(array(
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize2.mo')));
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo'));
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin(): void
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100));
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_FilesSize(array('min' => 100, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100));
        $this->assertEquals('1B', $validator->getMin());
    }

    /**
     * Ensures that setMin() returns expected value
     *
     * @return void
     */
    public function testSetMin(): void
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1000, 'max' => 10000));
        $validator->setMin(100);
        $this->assertEquals('100B', $validator->getMin());

        try {
            $validator->setMin(20000);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString("less than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax(): void
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100));
        $this->assertEquals('100B', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_FilesSize(array('min' => 100, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100000));
        $this->assertEquals('97.66kB', $validator->getMax());

        $validator = new Zend_Validate_File_FilesSize(2000);
        $validator->setUseByteString(false);
        $test = $validator->getMax();
        $this->assertEquals('2000', $test);
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax(): void
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1000, 'max' => 10000));
        $validator->setMax(1000000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());
    }

    public function testConstructorShouldRaiseErrorWhenPassedMultipleOptions(): void
    {
        $handler = set_error_handler(array($this, 'errorHandler'), E_USER_NOTICE);
        $validator = new Zend_Validate_File_FilesSize(1000, 10000);
        restore_error_handler();
        $this->assertInstanceOf(Zend_Validate_File_FilesSize::class, $validator);
    }

    /**
     * Ensures that the validator returns size infos
     *
     * @return void
     */
    public function testFailureMessage(): void
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 9999, 'max' => 10000));
        $this->assertFalse($validator->isValid(array(
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize2.mo')));
        $this->assertStringContainsString('9.76kB', current($validator->getMessages()));
        $this->assertStringContainsString('1.55kB', current($validator->getMessages()));

        $validator = new Zend_Validate_File_FilesSize(array('min' => 9999, 'max' => 10000, 'bytestring' => false));
        $this->assertFalse($validator->isValid(array(
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize2.mo')));
        $this->assertStringContainsString('9999', current($validator->getMessages()));
        $this->assertStringContainsString('1588', current($validator->getMessages()));
    }

    public function errorHandler($errno, $errstr)
    {
        if (strstr($errstr, 'deprecated')) {
            $this->multipleOptionsDetected = true;
        }
    }
}

// Call Zend_Validate_File_FilesSizeTest::main() if this source file is executed directly.
