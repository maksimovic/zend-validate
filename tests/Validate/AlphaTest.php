<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_AlphaTest extends TestCase
{
    /**
     * Zend_Validate_Alpha object
     *
     * @var Zend_Validate_Alpha
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Alpha object for each test method
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-filter package');
        $this->_validator = new Zend_Validate_Alpha();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => false,
            'aBcDeF'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages(): void
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testAllowWhiteSpace(): void
    {
        $this->_validator->setAllowWhiteSpace(true);

        $valuesExpected = array(
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => false,
            'aBcDeF'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            "a\tb c"  => true
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->_validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . "valid"
                );
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation(): void
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }
}
