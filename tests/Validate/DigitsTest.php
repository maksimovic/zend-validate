<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_DigitsTest extends TestCase
{
    /**
     * Zend_Validate_Digits object
     *
     * @var Zend_Validate_Digits
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Digits object for each test method
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-filter package');
        $this->_validator = new Zend_Validate_Digits();
    }

    /**
     * Ensures that the validator follows expected behavior for basic input values
     *
     * @return void
     */
    public function testExpectedResultsWithBasicInputValues(): void
    {
        $valuesExpected = array(
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => false,
            'AZ@#4.3' => false,
            '1.23'    => false,
            '0x9f'    => false,
            '123'     => true,
            '09'      => true,
            ''        => false
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
     * @return void
     */
    public function testEmptyStringValueResultsInProperValidationFailureMessages(): void
    {
        $this->assertFalse($this->_validator->isValid(''));
        $messages = $this->_validator->getMessages();
        $arrayExpected = array(
            Zend_Validate_Digits::STRING_EMPTY => '\'\' is an empty string'
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
            Zend_Validate_Digits::STRING_EMPTY
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
            Zend_Validate_Digits::NOT_DIGITS => '\'#\' must contain only digits'
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
            Zend_Validate_Digits::NOT_DIGITS
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
}
