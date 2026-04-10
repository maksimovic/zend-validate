<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_PostCodeTest extends TestCase
{
    /**
     * Zend_Validate_PostCode object
     *
     * @var Zend_Validate_PostCode
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_PostCode object for each test method
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-locale package');
        $this->_validator = new Zend_Validate_PostCode('de_AT');
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            array('2292', true),
            array('1000', true),
            array('0000', true),
            array('12345', false),
            array(1234, true),
            array(9821, true),
            array('21A4', false),
            array('ABCD', false),
            array(true, false),
            array('AT-2292', false),
            array(1.56, false)
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
     * Ensures that a region is available
     */
    public function testSettingLocalesWithoutRegion(): void
    {
        try {
            $this->_validator->setLocale('de');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('Unable to detect a region', $e->getMessage());
        }
    }

    /**
     * Ensures that the region contains postal codes
     */
    public function testSettingLocalesWithoutPostalCodes(): void
    {
        try {
            $this->_validator->setLocale('nus_SD');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('Unable to detect a postcode format', $e->getMessage());
        }
    }

    /**
     * Ensures locales can be retrieved
     */
    public function testGettingLocale(): void
    {
        $this->assertEquals('de_AT', $this->_validator->getLocale());
    }

    /**
     * Ensures format can be set and retrieved
     */
    public function testSetGetFormat(): void
    {
        $this->_validator->setFormat('\d{1}');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        $this->_validator->setFormat('/^\d{1}');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        $this->_validator->setFormat('/^\d{1}$/');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        $this->_validator->setFormat('\d{1}$/');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        try {
            $this->_validator->setFormat(null);
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('A postcode-format string has to be given', $e->getMessage());
        }

        try {
            $this->_validator->setFormat('');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('A postcode-format string has to be given', $e->getMessage());
        }
    }

    /**
     * @group ZF-9212
     */
    public function testErrorMessageText(): void
    {
        $this->assertFalse($this->_validator->isValid('hello'));
        $message = $this->_validator->getMessages();
        $this->assertStringContainsString('not appear to be a postal code', $message['postcodeNoMatch']);
    }
}

