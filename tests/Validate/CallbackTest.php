<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_CallbackTest extends TestCase
{

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        $this->assertTrue($valid->isValid('test'));
    }

    public function testStaticCallback(): void
    {
        $valid = new Zend_Validate_Callback(
            array('Zend_Validate_CallbackTest', 'staticCallback')
        );
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptionsAfterwards(): void
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        $valid->setOptions('options');
        $this->assertEquals(array('options'), $valid->getOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptions(): void
    {
        $valid = new Zend_Validate_Callback(array('callback' => array($this, 'objectCallback'), 'options' => 'options'));
        $this->assertEquals(array('options'), $valid->getOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testGettingCallback(): void
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        $this->assertEquals(array($this, 'objectCallback'), $valid->getCallback());
    }

    public function testInvalidCallback(): void
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        try {
            $valid->setCallback('invalidcallback');
            $this->fail('Exception expected');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Invalid callback given', $e->getMessage());
        }
    }

    public function testAddingValueOptions(): void
    {
        $valid = new Zend_Validate_Callback(array('callback' => array($this, 'optionsCallback'), 'options' => 'options'));
        $this->assertEquals(array('options'), $valid->getOptions());
        $this->assertTrue($valid->isValid('test', 'something'));
    }

    public function objectCallback($value)
    {
        return true;
    }

    public static function staticCallback($value)
    {
        return true;
    }

    public function optionsCallback($value)
    {
        $args = func_get_args();
        $this->assertContains('something', $args);
        return $args;
    }
}

