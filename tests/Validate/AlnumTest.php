<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_AlnumTest extends TestCase
{
    /**
     * Zend_Validate_Alnum object
     *
     * @var Zend_Validate_Alnum
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Alnum object for each test method
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-filter package');
        $this->_validator = new Zend_Validate_Alnum();
    }

    /**
     * Ensures that the validator follows expected behavior for basic input values
     *
     * @return void
     */
    public function testExpectedResultsWithBasicInputValues(): void
    {
        $valuesExpected = array(
            'abc123'  => true,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false,
            'foobar1' => true
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected initial value
     *
     * @return void
     */
    public function testMessagesEmptyInitially(): void
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testOptionToAllowWhiteSpaceWithBasicInputValues(): void
    {
        $this->_validator->setAllowWhiteSpace(true);

        $valuesExpected = array(
            'abc123'  => true,
            'abc 123' => true,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            'foobar1' => true
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
     * @return void
     */
    public function testEmptyStringValueResultsInProperValidationFailureMessages(): void
    {
        $this->assertFalse($this->_validator->isValid(''));
        $messages = $this->_validator->getMessages();
        $arrayExpected = array(
            Zend_Validate_Alnum::STRING_EMPTY => '\'\' is an empty string'
            );
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     * @deprecated Since 1.5.0
     */
    public function testEmptyStringValueResultsInProperValidationFailureErrors(): void
    {
        $this->assertFalse($this->_validator->isValid(''));
        $errors = $this->_validator->getErrors();
        $arrayExpected = array(
            Zend_Validate_Alnum::STRING_EMPTY
            );
        $this->assertThat($errors, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     */
    public function testInvalidValueResultsInProperValidationFailureMessages(): void
    {
        $this->assertFalse($this->_validator->isValid('#'));
        $messages = $this->_validator->getMessages();
        $arrayExpected = array(
            Zend_Validate_Alnum::NOT_ALNUM => '\'#\' contains characters which are non alphabetic and no digits'
            );
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     * @deprecated Since 1.5.0
     */
    public function testInvalidValueResultsInProperValidationFailureErrors(): void
    {
        $this->assertFalse($this->_validator->isValid('#'));
        $errors = $this->_validator->getErrors();
        $arrayExpected = array(
            Zend_Validate_Alnum::NOT_ALNUM
            );
        $this->assertThat($errors, $this->identicalTo($arrayExpected));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation(): void
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7475
     */
    public function testIntegerValidation(): void
    {
        $this->assertTrue($this->_validator->isValid(1));
    }
}
