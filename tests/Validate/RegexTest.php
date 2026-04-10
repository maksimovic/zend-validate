<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_RegexTest extends TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        /**
         * The elements of each array are, in order:
         *      - pattern
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array('/[a-z]/', true, array('abc123', 'foo', 'a', 'z')),
            array('/[a-z]/', false, array('123', 'A'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Regex($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input));
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages(): void
    {
        $validator = new Zend_Validate_Regex('/./');
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getPattern() returns expected value
     *
     * @return void
     */
    public function testGetPattern(): void
    {
        $validator = new Zend_Validate_Regex('/./');
        $this->assertEquals('/./', $validator->getPattern());
    }

    /**
     * Ensures that a bad pattern results in a thrown exception upon isValid() call
     *
     * @return void
     */
    public function testBadPattern(): void
    {
        try {
            $validator = new Zend_Validate_Regex('/');
            $validator->isValid('anything');
            $this->fail('Expected Zend_Validate_Exception not thrown for bad pattern');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('Internal error while', $e->getMessage());
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation(): void
    {
        $validator = new Zend_Validate_Regex('/./');
        $this->assertFalse($validator->isValid(array(1 => 1)));
    }
}
