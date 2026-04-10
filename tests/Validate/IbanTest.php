<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_IbanTest extends TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $validator = new Zend_Validate_Iban();
        $valuesExpected = array(
            'AD1200012030200359100100' => true,
            'AT611904300234573201'     => true,
            'AT61 1904 3002 3457 3201' => false,
            'AD1200012030200354100100' => false,
        );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input),
                                "'$input' expected to be " . ($result ? '' : 'in') . 'valid');
        }
    }

    public function testSettingAndGettingLocale(): void
    {
        $this->markTestSkipped('Requires zend-locale package');
        $validator = new Zend_Validate_Iban();
        try {
            $validator->setLocale('de_QA');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('IBAN validation', $e->getMessage());
        }

        $validator->setLocale('de_DE');
        $this->assertEquals('de_DE', $validator->getLocale());
    }

    public function testInstanceWithLocale(): void
    {
        $this->markTestSkipped('Requires zend-locale package');
        $validator = new Zend_Validate_Iban('de_AT');
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }

    public function testIbanNotSupported(): void
    {
        $this->markTestSkipped('Requires zend-locale package');
        $validator = new Zend_Validate_Iban('en_US');
        $this->assertFalse($validator->isValid('AT611904300234573201'));
    }

    /**
     * @group GH-641
     */
    public function testIbanOfMacedonia(): void
    {
        $validator = new Zend_Validate_Iban();
        $this->assertTrue($validator->isValid('MK07250120000058984'));
    }
}
