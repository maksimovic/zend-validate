<?php

use PHPUnit\Framework\TestCase;

class Zend_Validate_Sitemap_LocTest extends TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validate_Sitemap_Loc
     */
    protected $_validator;

    protected function setUp(): void
    {
        $this->markTestSkipped('Requires zend-uri package');
        $this->_validator = new Zend_Validate_Sitemap_Loc();
    }

    protected function tearDown(): void
    {
        $this->_validator = null;
    }

    public function testValidLocs(): void
    {
        $values = array(
            'http://www.example.com',
            'http://www.example.com/',
            'http://www.exmaple.lan/',
            'https://www.exmaple.com/?foo=bar',
            'http://www.exmaple.com:8080/foo/bar/',
            'https://user:pass@www.exmaple.com:8080/',
            'https://www.exmaple.com/?foo=&quot;bar&apos;&amp;bar=&lt;bat&gt;'
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }

    public function testInvalidLocs(): void
    {
        $values = array(
            'www.example.com',
            '/news/',
            '#',
            'http:/example.com/',
            'https://www.exmaple.com/?foo="bar\'&bar=<bat>'
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertStringContainsString('is not a valid', current($messages));
        }
    }

    public function testNotStrings(): void
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
