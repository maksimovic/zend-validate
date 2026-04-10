<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_BetweenTest extends TestCase
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
         *      - maximum
         *      - inclusive
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array(1, 100, true, true, array(1, 10, 100)),
            array(1, 100, true, false, array(0, 0.99, 100.01, 101)),
            array(1, 100, false, false, array(0, 1, 100, 101)),
            array('a', 'z', true, true, array('a', 'b', 'y', 'z')),
            array('a', 'z', false, false, array('!', 'a', 'z'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Between(array('min' => $element[0], 'max' => $element[1], 'inclusive' => $element[2]));
            foreach ($element[4] as $input) {
                $this->assertEquals($element[3], $validator->isValid($input),
                'Failed values: ' . $input . ":" . implode("\n", $validator->getMessages()));
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
        $validator = new Zend_Validate_Between(array('min' => 1, 'max' => 10));
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin(): void
    {
        $validator = new Zend_Validate_Between(array('min' => 1, 'max' => 10));
        $this->assertEquals(1, $validator->getMin());
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax(): void
    {
        $validator = new Zend_Validate_Between(array('min' => 1, 'max' => 10));
        $this->assertEquals(10, $validator->getMax());
    }

    /**
     * Ensures that getInclusive() returns expected default value
     *
     * @return void
     */
    public function testGetInclusive(): void
    {
        $validator = new Zend_Validate_Between(array('min' => 1, 'max' => 10));
        $this->assertEquals(true, $validator->getInclusive());
    }
}
