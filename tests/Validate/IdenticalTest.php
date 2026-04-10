<?php

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.


/**
 * Zend_Validate_Identical
 *
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @uses       Zend_Validate_Identical
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */

use PHPUnit\Framework\TestCase;

class Zend_Validate_IdenticalTest extends TestCase
{
    /**
     * @var Zend_Validate_Identical
     */
    private $validator;

    public function setUp(): void
    {
        $this->validator = new Zend_Validate_Identical;
    }

    public function testTokenInitiallyNull(): void
    {
        $this->assertNull($this->validator->getToken());
    }

    public function testCanSetToken(): void
    {
        $this->testTokenInitiallyNull();
        $this->validator->setToken('foo');
        $this->assertEquals('foo', $this->validator->getToken());
    }

    public function testCanSetTokenViaConstructor(): void
    {
        $validator = new Zend_Validate_Identical('foo');
        $this->assertEquals('foo', $validator->getToken());
    }

    public function testValidatingWhenTokenNullReturnsFalse(): void
    {
        $this->assertFalse($this->validator->isValid('foo'));
    }

    public function testValidatingWhenTokenNullSetsMissingTokenMessage(): void
    {
        $this->testValidatingWhenTokenNullReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('missingToken', $messages));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueReturnsFalse(): void
    {
        $this->validator->setToken('foo');
        $this->assertFalse($this->validator->isValid('bar'));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueSetsNotSameMessage(): void
    {
        $this->testValidatingAgainstTokenWithNonMatchingValueReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('notSame', $messages));
    }

    public function testValidatingAgainstTokenWithMatchingValueReturnsTrue(): void
    {
        $this->validator->setToken('foo');
        $this->assertTrue($this->validator->isValid('foo'));
    }

    /**
     * @group ZF-6953
     */
    public function testValidatingAgainstEmptyToken(): void
    {
        $this->validator->setToken('');
        $this->assertTrue($this->validator->isValid(''));
    }

    /**
     * @group ZF-7128
     */
    public function testValidatingAgainstNonStrings(): void
    {
        $this->validator->setToken(true);
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(1));

        $this->validator->setToken(array('one' => 'two', 'three'));
        $this->assertTrue($this->validator->isValid(array('one' => 'two', 'three')));
        $this->assertFalse($this->validator->isValid(array()));
    }

    public function testValidatingTokenArray(): void
    {
        $validator = new Zend_Validate_Identical(array('token' => 123));
        $this->assertTrue($validator->isValid(123));
        $this->assertFalse($validator->isValid(array('token' => 123)));
    }

    public function testValidatingNonStrictToken(): void
    {
        $validator = new Zend_Validate_Identical(array('token' => 123, 'strict' => false));
        $this->assertTrue($validator->isValid('123'));

        $validator->setStrict(true);
        $this->assertFalse($validator->isValid(array('token' => '123')));
    }
}

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.
