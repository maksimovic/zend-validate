<?php


/**
 * Tests Zym_Validate_Sitemap_Changefreq
 *
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_Sitemap_ChangefreqTest extends TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validate_Sitemap_Changefreq
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp(): void
    {
        $this->_validator = new Zend_Validate_Sitemap_Changefreq();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown(): void
    {
        $this->_validator = null;
    }

    /**
     * Tests valid change frequencies
     *
     */
    public function testValidChangefreqs(): void
    {
        $values = array(
            'always',  'hourly', 'daily', 'weekly',
            'monthly', 'yearly', 'never'
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }

    /**
     * Tests strings that should be invalid
     *
     */
    public function testInvalidStrings(): void
    {
        $values = array(
            'alwayz',  '_hourly', 'Daily', 'wEekly',
            'mönthly ', ' yearly ', 'never ', 'rofl',
            'yesterday',
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertStringContainsString('is not a valid', current($messages));
        }
    }

    /**
     * Tests values that are not strings
     *
     */
    public function testNotString(): void
    {
        $values = array(
            1, 1.4, null, new stdClass(), true, false
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertStringContainsString('String expected', current($messages));
        }
    }
}
