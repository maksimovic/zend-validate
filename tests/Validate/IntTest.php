<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_IntTest extends TestCase
{
    /**
     * Zend_Validate_Int object
     *
     * @var Zend_Validate_Int
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Int object for each test method
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-locale package');
        $this->_validator = new Zend_Validate_Int();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $this->_validator->setLocale('en');
        $valuesExpected = array(
            array(1.00, true),
            array(0.00, true),
            array(0.01, false),
            array(-0.1, false),
            array(-1, true),
            array('10', true),
            array(1, true),
            array('not an int', false),
            array(true, false),
            array(false, false),
            );

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
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
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales(): void
    {
        $this->_validator->setLocale('de');
        $this->assertEquals('de', $this->_validator->getLocale());
        $this->assertEquals(false, $this->_validator->isValid('10 000'));
        $this->assertEquals(true, $this->_validator->isValid('10.000'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation(): void
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7489
     */
    public function testUsingApplicationLocale(): void
    {
        Zend_Registry::set('Zend_Locale', new Zend_Locale('de'));
        $valid = new Zend_Validate_Int();
        $this->assertTrue($valid->isValid('10.000'));
    }

    /**
     * @ZF-7703
     */
    public function testLocaleDetectsNoEnglishLocaleOnOtherSetLocale(): void
    {
        Zend_Registry::set('Zend_Locale', new Zend_Locale('de'));
        $valid = new Zend_Validate_Int();
        $this->assertTrue($valid->isValid(1200));
        $this->assertFalse($valid->isValid('1,200'));
    }
}
