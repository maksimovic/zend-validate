<?php


/**
 * PHPUnit_Framework_TestCase
 */


/**
 * Mock No Result Adapter
 */

/**
 * Mock Result Adapter
 */


use PHPUnit\Framework\TestCase;

class Zend_Validate_Db_RecordExistsTest extends TestCase
{

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapterHasResult;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapterNoResult;

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->markTestSkipped('Requires zend-db package');
        $this->_adapterHasResult = new Db_MockHasResult();
        $this->_adapterNoResult = new Db_MockNoResult();

    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsRecord(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users', 'field' => 'field1'));
        $this->assertTrue($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users', 'field' => 'field1'));
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users', 'field' => 'field1', 'exclude' => array('field' => 'id', 'value' => 1)));
        $this->assertTrue($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with an array
     *
     * @return void
     */
    public function testExcludeWithArrayNoRecord(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users', 'field' => 'field1', 'exclude' => array('field' => 'id', 'value' => 1)));
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithString(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users', 'field' => 'field1', 'exclude' => 'id != 1'));
        $this->assertTrue($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithStringNoRecord(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1', 'id != 1');
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * Test that the class throws an exception if no adapter is provided
     * and no default is set.
     *
     * @return void
     */
    public function testThrowsExceptionWithNoAdapter(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_RecordExists('users', 'field1', 'id != 1');
            $valid = $validator->isValid('nosuchvalue');
            $this->markTestFailed('Did not throw exception');
        } catch (Exception $e) {
        }
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchema(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users',
                                                             'schema' => 'my',
                                                             'field' => 'field1'));
        $this->assertTrue($validator->isValid('value1'));
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchemaNoResult(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users',
                                                             'schema' => 'my',
                                                             'field' => 'field1'));
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test when adapter is provided
     *
     * @return void
     */
    public function testAdapterProvided(): void
    {
        //clear the default adapter to ensure provided one is used
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_RecordExists('users', 'field1', null, $this->_adapterHasResult);
            $this->assertTrue($validator->isValid('value1'));
        } catch (Zend_Exception $e) {
            $this->markTestSkipped('No database available');
        }
    }

    /**
     * Test when adapter is provided
     *
     * @return void
     */
    public function testAdapterProvidedNoResult(): void
    {
        //clear the default adapter to ensure provided one is used
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_RecordExists('users', 'field1', null, $this->_adapterNoResult);
            $this->assertFalse($validator->isValid('value1'));
        } catch (Exception $e) {
            $this->markTestSkipped('No database available');
        }
    }

    /**
     * @return ZF-8863
     */
    public function testExcludeConstructor(): void
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1', 'id != 1');
        $this->assertTrue($validator->isValid('value3'));
    }
}
