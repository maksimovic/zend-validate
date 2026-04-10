<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_InArrayTest extends TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $validator = new Zend_Validate_InArray(array(1, 'a', 2.3));
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(1.0));
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid('a'));
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(2.3));
        $this->assertTrue($validator->isValid(2.3e0));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages(): void
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getHaystack() returns expected value
     *
     * @return void
     */
    public function testGetHaystack(): void
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $validator->getHaystack());
    }

    /**
     * Ensures that getStrict() returns expected default value
     *
     * @return void
     */
    public function testGetStrict(): void
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertFalse($validator->getStrict());
    }

    public function testGivingOptionsAsArrayAtInitiation(): void
    {
        $validator = new Zend_Validate_InArray(
            array('haystack' =>
                array(1, 'a', 2.3)
            )
        );
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(1.0));
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid('a'));
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(2.3));
        $this->assertTrue($validator->isValid(2.3e0));
    }

    public function testSettingANewHaystack(): void
    {
        $validator = new Zend_Validate_InArray(
            array('haystack' =>
                array('test', 0, 'A')
            )
        );
        $this->assertTrue($validator->isValid('A'));

        $validator->setHaystack(array(1, 'a', 2.3));
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(1.0));
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid('a'));
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(2.3));
        $this->assertTrue($validator->isValid(2.3e0));
    }

    public function testSettingNewStrictMode(): void
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertFalse($validator->getStrict());
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid(1));

        $validator->setStrict(true);
        $this->assertTrue($validator->getStrict());
        $this->assertFalse($validator->isValid('1'));
        $this->assertTrue($validator->isValid(1));
    }

    public function testSettingStrictViaInitiation(): void
    {
        $validator = new Zend_Validate_InArray(
            array(
                'haystack' => array('test', 0, 'A'),
                'strict'   => true
            )
        );
        $this->assertTrue($validator->getStrict());
    }

    public function testGettingRecursiveOption(): void
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertFalse($validator->getRecursive());

        $validator->setRecursive(true);
        $this->assertTrue($validator->getRecursive());
    }

    public function testSettingRecursiveViaInitiation(): void
    {
        $validator = new Zend_Validate_InArray(
            array(
                'haystack'  => array('test', 0, 'A'),
                'recursive' => true
            )
        );
        $this->assertTrue($validator->getRecursive());
    }

    public function testRecursiveDetection(): void
    {
        $validator = new Zend_Validate_InArray(
            array(
                'haystack'  =>
                    array(
                        'firstDimension' => array('test', 0, 'A'),
                        'secondDimension' => array('value', 2, 'a')),
                'recursive' => false
            )
        );
        $this->assertFalse($validator->isValid('A'));

        $validator->setRecursive(true);
        $this->assertTrue($validator->isValid('A'));
    }

    public function testRecursiveStandalone(): void
    {
        $validator = new Zend_Validate_InArray(
            array(
                'firstDimension' => array('test', 0, 'A'),
                'secondDimension' => array('value', 2, 'a')
            )
        );
        $this->assertFalse($validator->isValid('A'));

        $validator->setRecursive(true);
        $this->assertTrue($validator->isValid('A'));
    }

    /**
     * @group GH-365
     */
    public function testMultidimensionalArrayNotFound(): void
    {
        $input = array(
            array('x'),
            array('y'),
        );
        $validator = new Zend_Validate_InArray(array('a'));
        $this->assertFalse($validator->isValid($input));
    }

    /**
     * @group GH-365
     */
    public function testErrorMessageWithArrayValue(): void
    {
        $input = array(
            array('x'),
            array('y'),
        );
        $validator = new Zend_Validate_InArray(array('a'));
        $validator->isValid($input);
        $messages  = $validator->getMessages();
        $this->assertEquals(
            "'x, y' was not found in the haystack",
            current($messages)
        );
    }
}
