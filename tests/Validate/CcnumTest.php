<?php


use PHPUnit\Framework\TestCase;

class Zend_Validate_CcnumTest extends TestCase
{
    /**
     * Zend_Validate_Ccnum object
     *
     * @var Zend_Validate_Ccnum
     */
    protected $_validator;

    /**
     * @var bool
     */
    protected $_errorOccured;

    /**
     * Creates a new Zend_Validate_Ccnum object for each test method
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-loader package');
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->_validator = new Zend_Validate_Ccnum();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic(): void
    {
        $valuesExpected = array(
            '4929000000006'    => true,
            '5404000000000001' => true,
            '374200000000004'  => true,
            '4444555566667777' => false,
            'ABCDEF'           => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
        }
        restore_error_handler();
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages(): void
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
        restore_error_handler();
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext = array())
    {
        $this->_errorOccured = true;
    }
}
