<?php


/**
 * Zend_Validate_Barcode
 *
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @uses       Zend_Validate_Barcode
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_BarcodeTest extends TestCase
{
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-loader package');
    }

    /**
     * Test if EAN-13 contains only numeric characters
     *
     * @group ZF-3297
     */
    public function testEan13ContainsOnlyNumeric(): void
    {
        $barcode = new Zend_Validate_Barcode('ean13');
        $this->assertFalse($barcode->isValid('3RH1131-1BB40'));
    }

    public function testNoneExisting(): void
    {
        try {
            $barcode = new Zend_Validate_Barcode('Zend_Validate_BarcodeTest_NonExistentClassName');
            $this->fail("'Zend_Validate_BarcodeTest_NonExistentClassName' is not a valid barcode type'");
        } catch (Exception $e) {
            $this->assertMatchesRegularExpression('#could not be found#', $e->getMessage());
        }
    }

    public function testSetAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('upca');
        $this->assertTrue($barcode->isValid('065100004327'));

        $barcode->setAdapter('ean13');
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation(): void
    {
        $barcode = new Zend_Validate_Barcode('upca');
        $this->assertFalse($barcode->isValid(106510000.4327));
        $this->assertFalse($barcode->isValid(array('065100004327')));

        $barcode = new Zend_Validate_Barcode('ean13');
        $this->assertFalse($barcode->isValid(06510000.4327));
        $this->assertFalse($barcode->isValid(array('065100004327')));
    }

    public function testInvalidChecksumAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode1');
        $this->assertFalse($barcode->isValid('0000000'));
        $this->assertTrue(array_key_exists('barcodeFailed', $barcode->getMessages()));
        $this->assertFalse($barcode->getAdapter()->checksum('0000000'));
    }

    public function testInvalidCharAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode1');
        $this->assertFalse($barcode->getAdapter()->checkChars(123));
    }

    public function testAscii128CharacterAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->checkChars('1234QW!"'));
    }

    public function testInvalidLengthAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode2');
        $this->assertFalse($barcode->getAdapter()->checkLength(123));
    }

    public function testArrayLengthAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->checkLength('1'));
        $this->assertFalse($barcode->getAdapter()->checkLength('12'));
        $this->assertTrue($barcode->getAdapter()->checkLength('123'));
        $this->assertFalse($barcode->getAdapter()->checkLength('1234'));
    }

    public function testArrayLengthAdapter2(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode3');
        $this->assertTrue($barcode->getAdapter()->checkLength('1'));
        $this->assertTrue($barcode->getAdapter()->checkLength('12'));
        $this->assertTrue($barcode->getAdapter()->checkLength('123'));
        $this->assertTrue($barcode->getAdapter()->checkLength('1234'));
    }

    public function testOddLengthAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('MyBarcode4');
        $this->assertTrue($barcode->getAdapter()->checkLength('1'));
        $this->assertFalse($barcode->getAdapter()->checkLength('12'));
        $this->assertTrue($barcode->getAdapter()->checkLength('123'));
        $this->assertFalse($barcode->getAdapter()->checkLength('1234'));
    }

    public function testInvalidAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode('Ean13');
        try {
            $barcode->setAdapter('MyBarcode5');
            $this->fails('Exception expected');
        } catch (Exception $e) {
            $this->assertStringContainsString('does not implement', $e->getMessage());
        }
    }

    public function testArrayConstructAdapter(): void
    {
        $barcode = new Zend_Validate_Barcode(array('adapter' => 'Ean13', 'options' => 'unknown', 'checksum' => false));
        $this->assertTrue($barcode->getAdapter() instanceof Zend_Validate_Barcode_Ean13);
        $this->assertFalse($barcode->getChecksum());
    }

    public function testInvalidArrayConstructAdapter(): void
    {
        try {
            $barcode = new Zend_Validate_Barcode(array('options' => 'unknown', 'checksum' => false));
            $this->fails('Exception expected');
        } catch (Exception $e) {
            $this->assertStringContainsString('Missing option', $e->getMessage());
        }
    }

    public function testConfigConstructAdapter(): void
    {
        $array = array('adapter' => 'Ean13', 'options' => 'unknown', 'checksum' => false);
        $config = new Zend_Config($array);

        $barcode = new Zend_Validate_Barcode($config);
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    public function testCODE25(): void
    {
        $barcode = new Zend_Validate_Barcode('code25');
        $this->assertTrue($barcode->isValid('0123456789101213'));
        $this->assertTrue($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123a'));

        $barcode->setChecksum(true);
        $this->assertTrue($barcode->isValid('0123456789101214'));
        $this->assertFalse($barcode->isValid('0123456789101213'));
    }

    public function testCODE25INTERLEAVED(): void
    {
        $barcode = new Zend_Validate_Barcode('code25interleaved');
        $this->assertTrue($barcode->isValid('0123456789101213'));
        $this->assertFalse($barcode->isValid('123'));

        $barcode->setChecksum(true);
        $this->assertTrue($barcode->isValid('0123456789101214'));
        $this->assertFalse($barcode->isValid('0123456789101213'));
    }

    public function testCODE39(): void
    {
        $barcode = new Zend_Validate_Barcode('code39');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertTrue($barcode->isValid('00075678164124'));
        $this->assertFalse($barcode->isValid('Test93Test93Test'));

        $barcode->setChecksum(true);
        $this->assertTrue($barcode->isValid('159AZH'));
        $this->assertFalse($barcode->isValid('159AZG'));
    }

    public function testCODE39EXT(): void
    {
        $barcode = new Zend_Validate_Barcode('code39ext');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertTrue($barcode->isValid('00075678164124'));
        $this->assertTrue($barcode->isValid('Test93Test93Test'));

// @TODO: CODE39 EXTENDED CHECKSUM VALIDATION MISSING
//        $barcode->setChecksum(true);
//        $this->assertTrue($barcode->isValid('159AZH'));
//        $this->assertFalse($barcode->isValid('159AZG'));
    }

    public function testCODE93(): void
    {
        $barcode = new Zend_Validate_Barcode('code93');
        $this->assertTrue($barcode->isValid('TEST93+'));
        $this->assertFalse($barcode->isValid('Test93+'));

        $barcode->setChecksum(true);
        $this->assertTrue($barcode->isValid('CODE 93E0'));
        $this->assertFalse($barcode->isValid('CODE 93E1'));
    }

    public function testCODE93EXT(): void
    {
        $barcode = new Zend_Validate_Barcode('code93ext');
        $this->assertTrue($barcode->isValid('TEST93+'));
        $this->assertTrue($barcode->isValid('Test93+'));

// @TODO: CODE93 EXTENDED CHECKSUM VALIDATION MISSING
//        $barcode->setChecksum(true);
//        $this->assertTrue($barcode->isValid('CODE 93E0'));
//        $this->assertFalse($barcode->isValid('CODE 93E1'));
    }

    public function testEAN2(): void
    {
        $barcode = new Zend_Validate_Barcode('ean2');
        $this->assertTrue($barcode->isValid('12'));
        $this->assertFalse($barcode->isValid('1'));
        $this->assertFalse($barcode->isValid('123'));
    }

    public function testEAN5(): void
    {
        $barcode = new Zend_Validate_Barcode('ean5');
        $this->assertTrue($barcode->isValid('12345'));
        $this->assertFalse($barcode->isValid('1234'));
        $this->assertFalse($barcode->isValid('123456'));
    }

    public function testEAN8(): void
    {
        $barcode = new Zend_Validate_Barcode('ean8');
        $this->assertTrue($barcode->isValid('12345670'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345671'));
        $this->assertTrue($barcode->isValid('1234567'));
    }

    public function testEAN12(): void
    {
        $barcode = new Zend_Validate_Barcode('ean12');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testEAN13(): void
    {
        $barcode = new Zend_Validate_Barcode('ean13');
        $this->assertTrue($barcode->isValid('1234567890128'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1234567890127'));
    }

    public function testEAN14(): void
    {
        $barcode = new Zend_Validate_Barcode('ean14');
        $this->assertTrue($barcode->isValid('12345678901231'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345678901232'));
    }

    public function testEAN18(): void
    {
        $barcode = new Zend_Validate_Barcode('ean18');
        $this->assertTrue($barcode->isValid('123456789012345675'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789012345676'));
    }

    public function testGTIN12(): void
    {
        $barcode = new Zend_Validate_Barcode('gtin12');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testGTIN13(): void
    {
        $barcode = new Zend_Validate_Barcode('gtin13');
        $this->assertTrue($barcode->isValid('1234567890128'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1234567890127'));
    }

    public function testGTIN14(): void
    {
        $barcode = new Zend_Validate_Barcode('gtin14');
        $this->assertTrue($barcode->isValid('12345678901231'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345678901232'));
    }

    public function testIDENTCODE(): void
    {
        $barcode = new Zend_Validate_Barcode('identcode');
        $this->assertTrue($barcode->isValid('564000000050'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('0563102430313'));
        $this->assertFalse($barcode->isValid('564000000051'));
    }

    public function testINTELLIGENTMAIL(): void
    {
        $barcode = new Zend_Validate_Barcode('intelligentmail');
        $this->assertTrue($barcode->isValid('01234567094987654321'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('5555512371'));
    }

    public function testISSN(): void
    {
        $barcode = new Zend_Validate_Barcode('issn');
        $this->assertTrue($barcode->isValid('1144875X'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1144874X'));

        $this->assertTrue($barcode->isValid('9771144875007'));
    }

    public function testITF14(): void
    {
        $barcode = new Zend_Validate_Barcode('itf14');
        $this->assertTrue($barcode->isValid('00075678164125'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('00075678164124'));
    }

    public function testLEITCODE(): void
    {
        $barcode = new Zend_Validate_Barcode('leitcode');
        $this->assertTrue($barcode->isValid('21348075016401'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('021348075016401'));
        $this->assertFalse($barcode->isValid('21348075016402'));
    }

    public function testPLANET(): void
    {
        $barcode = new Zend_Validate_Barcode('planet');
        $this->assertTrue($barcode->isValid('401234567891'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('401234567892'));
    }

    public function testPOSTNET(): void
    {
        $barcode = new Zend_Validate_Barcode('postnet');
        $this->assertTrue($barcode->isValid('5555512372'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('5555512371'));
    }

    public function testROYALMAIL(): void
    {
        $barcode = new Zend_Validate_Barcode('royalmail');
        $this->assertTrue($barcode->isValid('SN34RD1AK'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('SN34RD1AW'));

        $this->assertTrue($barcode->isValid('012345W'));
        $this->assertTrue($barcode->isValid('06CIOUH'));
    }

    public function testSSCC(): void
    {
        $barcode = new Zend_Validate_Barcode('sscc');
        $this->assertTrue($barcode->isValid('123456789012345675'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789012345676'));
    }

    public function testUPCA(): void
    {
        $barcode = new Zend_Validate_Barcode('upca');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testUPCE(): void
    {
        $barcode = new Zend_Validate_Barcode('upce');
        $this->assertTrue($barcode->isValid('02345673'));
        $this->assertFalse($barcode->isValid('02345672'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertTrue($barcode->isValid('123456'));
        $this->assertTrue($barcode->isValid('0234567'));
    }

    /**
     * @group ZF-10116
     */
    public function testArrayLengthMessage(): void
    {
        $barcode = new Zend_Validate_Barcode('ean8');
        $this->assertFalse($barcode->isValid('123'));
        $message = $barcode->getMessages();
        $this->assertTrue(array_key_exists('barcodeInvalidLength', $message));
        $this->assertStringContainsString("length of 7/8 characters", $message['barcodeInvalidLength']);
    }
}
