<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_GreaterThanTest extends TestCase
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
         *      - minimum
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array(0, true, array(0.01, 1, 100)),
            array(0, false, array(0, 0.00, -0.01, -1, -100)),
            array('a', true, array('b', 'c', 'd')),
            array('z', false, array('x', 'y', 'z'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_GreaterThan($element[0]);
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
        $validator = new Zend_Validate_GreaterThan(10);
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin(): void
    {
        $validator = new Zend_Validate_GreaterThan(10);
        $this->assertEquals(10, $validator->getMin());
    }
}
