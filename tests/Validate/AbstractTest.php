<?php

use PHPUnit\Framework\TestCase;

class Zend_Validate_AbstractTest extends TestCase
{
    /**
     * @var Zend_Validate_AbstractTest_Concrete
     */
    protected $validator;

    /**
     * @var bool
     */
    protected $_errorOccurred;

    public function clearRegistry(): void
    {
        if (Zend_Registry::isRegistered('Zend_Translate')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Translate']);
        }
    }

    public function setUp(): void
    {
        $this->clearRegistry();
        Zend_Validate_Abstract::setDefaultTranslator(null);
        $this->validator = new Zend_Validate_AbstractTest_Concrete();
    }

    public function tearDown(): void
    {
        $this->clearRegistry();
        Zend_Validate_Abstract::setDefaultTranslator(null);
        Zend_Validate_Abstract::setMessageLength(-1);
    }

    public function testTranslatorNullByDefault(): void
    {
        $this->assertNull($this->validator->getTranslator());
    }

    public function testCanSetTranslator(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testCanSetTranslatorToNull(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testGlobalDefaultTranslatorNullByDefault(): void
    {
        $this->assertNull(Zend_Validate_Abstract::getDefaultTranslator());
    }

    public function testCanSetGlobalDefaultTranslator(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testGlobalDefaultTranslatorUsedWhenNoLocalTranslatorSet(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testGlobalTranslatorFromRegistryUsedWhenNoLocalTranslatorSet(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testLocalTranslatorPreferredOverGlobalTranslator(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testErrorMessagesAreTranslatedWhenTranslatorPresent(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testCanTranslateMessagesInsteadOfKeys(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testObscureValueFlagFalseByDefault(): void
    {
        $this->assertFalse($this->validator->getObscureValue());
    }

    public function testCanSetObscureValueFlag(): void
    {
        $this->testObscureValueFlagFalseByDefault();
        $this->validator->setObscureValue(true);
        $this->assertTrue($this->validator->getObscureValue());
        $this->validator->setObscureValue(false);
        $this->assertFalse($this->validator->getObscureValue());
    }

    public function testValueIsObfuscatedWheObscureValueFlagIsTrue(): void
    {
        $this->validator->setObscureValue(true);
        $this->assertFalse($this->validator->isValid('foobar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(isset($messages['fooMessage']));
        $message = $messages['fooMessage'];
        $this->assertStringNotContainsString('foobar', $message);
        $this->assertStringContainsString('******', $message);
    }

    public function testDoesNotFailOnObjectInput(): void
    {
        $this->assertFalse($this->validator->isValid(new stdClass()));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
    }

    public function testTranslatorEnabledPerDefault(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testCanDisableTranslator(): void
    {
        $this->markTestSkipped('Requires zend-translate package');
    }

    public function testGetMessageTemplates(): void
    {
        $messages = $this->validator->getMessageTemplates();
        $this->assertEquals(
            array('fooMessage' => '%value% was passed'), $messages);

        $this->assertEquals(
            array(
                Zend_Validate_AbstractTest_Concrete::FOO_MESSAGE => '%value% was passed'), $messages);
    }

    public function testMaximumErrorMessageLength(): void
    {
        $this->assertEquals(-1, Zend_Validate::getMessageLength());
        Zend_Validate_Abstract::setMessageLength(10);
        $this->assertEquals(10, Zend_Validate::getMessageLength());

        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertEquals(10, strlen($messages['fooMessage']));
    }

    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext = array()): void
    {
        $this->_errorOccurred = true;
    }
}

class Zend_Validate_AbstractTest_Concrete extends Zend_Validate_Abstract
{
    const FOO_MESSAGE = 'fooMessage';

    protected $_messageTemplates = array(
        'fooMessage' => '%value% was passed',
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        $this->_error(self::FOO_MESSAGE);
        return false;
    }
}
