<?php


/**
 * Tests Zend_Validate_Sitemap_Priority
 *
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_Sitemap_PriorityTest extends TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validate_Sitemap_Priority
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp(): void
    {
        $this->_validator = new Zend_Validate_Sitemap_Priority();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown(): void
    {
        $this->_validator = null;
    }

    /**
     * Tests valid priorities
     *
     */
    public function testValidPriorities(): void
    {
        $values = array(
            '0.0', '0.1', '0.2', '0.3', '0.4', '0.5',
            '0.6', '0.7', '0.8', '0.9', '1.0', '0.99',
            0.1, 0.6667, 0.0001, 0.4, 0, 1, .35
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }

    /**
     * Tests invalid priorities
     *
     */
    public function testInvalidPriorities(): void
    {
        $values = array(
            -1, -0.1, 1.1, 100, 10, 2, '3', '-4',
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertStringContainsString('is not a valid', current($messages));
        }
    }

    /**
     * Tests values that are no numbers
     *
     */
    public function testNotNumbers(): void
    {
        $values = array(
            null, new stdClass(), true, false, 'abcd',
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertStringContainsString('integer or float expected', current($messages));
        }
    }
}
