<?php

// Call Zend_Validate_File_WordCountTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_WordCountTest::main");
}


use PHPUnit\Framework\TestCase;

class Zend_Validate_File_WordCountTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array(15, true),
            array(4, false),
            array(array('min' => 0, 'max' => 10), true),
            array(array('min' => 10, 'max' => 15), false),
            );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_WordCount($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/wordcount.txt'),
                "Tested with " . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin(): void
    {
        $validator = new Zend_Validate_File_WordCount(array('min' => 1, 'max' => 5));
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new Zend_Validate_File_WordCount(array('min' => 5, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_WordCount(array('min' => 1, 'max' => 5));
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new Zend_Validate_File_WordCount(array('min' => 5, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setMin() returns expected value
     *
     * @return void
     */
    public function testSetMin(): void
    {
        $validator = new Zend_Validate_File_WordCount(array('min' => 1000, 'max' => 10000));
        $validator->setMin(100);
        $this->assertEquals(100, $validator->getMin());

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
        $validator = new Zend_Validate_File_WordCount(array('min' => 1, 'max' => 100));
        $this->assertEquals(100, $validator->getMax());

        try {
            $validator = new Zend_Validate_File_WordCount(array('min' => 5, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax(): void
    {
        $validator = new Zend_Validate_File_WordCount(array('min' => 1000, 'max' => 10000));
        $validator->setMax(1000000);
        $this->assertEquals(1000000, $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals(1000000, $validator->getMax());
    }
}

// Call Zend_Validate_File_WordCountTest::main() if this source file is executed directly.
