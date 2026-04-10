<?php

use PHPUnit\Framework\TestCase;

class Zend_ValidateTest extends TestCase
{
    /**
     * @var bool
     */
    protected $error;

    /**
     * @var bool
     */
    protected $_errorOccurred;

    /**
     * @var Zend_Validate
     */
    protected $_validator;

    public function setUp(): void
    {
        $this->_validator = new Zend_Validate();
    }

    public function tearDown(): void
    {
        Zend_Validate::setDefaultNamespaces(array());
    }

    public function testEmpty(): void
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
        $this->assertTrue($this->_validator->isValid('something'));
        $this->assertEquals(array(), $this->_validator->getErrors());
    }

    public function testTrue(): void
    {
        $this->_validator->addValidator(new Zend_ValidateTest_True());
        $this->assertTrue($this->_validator->isValid(null));
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
    }

    public function testFalse(): void
    {
        $this->_validator->addValidator(new Zend_ValidateTest_False());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->_validator->getMessages());
    }

    public function testBreakChainOnFailure(): void
    {
        $this->_validator->addValidator(new Zend_ValidateTest_False(), true)
                         ->addValidator(new Zend_ValidateTest_False());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->_validator->getMessages());
    }

    public function testStaticFactory(): void
    {
        $this->assertTrue(Zend_Validate::is('test@example.com', 'EmailAddress'));
        $this->assertFalse(Zend_Validate::is('not-an-email', 'EmailAddress'));
    }

    public function testStaticFactoryWithConstructorArguments(): void
    {
        $this->assertTrue(Zend_Validate::is('12', 'Between', array('min' => 1, 'max' => 12)));
        $this->assertFalse(Zend_Validate::is('24', 'Between', array('min' => 1, 'max' => 12)));
    }

    public function testStaticFactoryClassNotFound(): void
    {
        $this->expectException(Zend_Validate_Exception::class);
        Zend_Validate::is('1234', 'UnknownValidator');
    }

    public function testNamespaces(): void
    {
        $this->assertEquals(array(), Zend_Validate::getDefaultNamespaces());
        $this->assertFalse(Zend_Validate::hasDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces('TestDir');
        $this->assertEquals(array('TestDir'), Zend_Validate::getDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces('OtherTestDir');
        $this->assertEquals(array('OtherTestDir'), Zend_Validate::getDefaultNamespaces());

        $this->assertTrue(Zend_Validate::hasDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces(array());

        $this->assertEquals(array(), Zend_Validate::getDefaultNamespaces());
        $this->assertFalse(Zend_Validate::hasDefaultNamespaces());

        Zend_Validate::addDefaultNamespaces(array('One', 'Two'));
        $this->assertEquals(array('One', 'Two'), Zend_Validate::getDefaultNamespaces());

        Zend_Validate::addDefaultNamespaces('Three');
        $this->assertEquals(array('One', 'Two', 'Three'), Zend_Validate::getDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces(array());
    }

    public function testIsValidWithParameters(): void
    {
        $this->assertTrue(Zend_Validate::is(5, 'Between', array(1, 10)));
        $this->assertTrue(Zend_Validate::is(5, 'Between', array('min' => 1, 'max' => 10)));
    }

    public function testSetGetMessageLengthLimitation(): void
    {
        Zend_Validate::setMessageLength(5);
        $this->assertEquals(5, Zend_Validate::getMessageLength());

        $valid = new Zend_Validate_Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertTrue(strlen($message) <= 5);
    }

    public function testSetGetDefaultTranslator(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function handleNotFoundError($errnum, $errstr): void
    {
        if (strstr($errstr, 'No such file')) {
            $this->error = true;
        }
    }

    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext = array()): void
    {
        $this->_errorOccurred = true;
    }
}


class Zend_ValidateTest_True extends Zend_Validate_Abstract
{
    public function isValid($value)
    {
        return true;
    }
}


class Zend_ValidateTest_False extends Zend_Validate_Abstract
{
    public function isValid($value)
    {
        $this->_messages = array('error' => 'validation failed');
        return false;
    }
}
