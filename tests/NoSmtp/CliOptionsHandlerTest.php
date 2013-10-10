<?php
namespace NoSmtp;

class CliOptionsHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $opts_handler;
    protected $raw_options;

    protected function setUp()
    {
        $this->opts_handler = new CliOptionsHandler;
        $this->raw_options  = array();
    }

    public function testValidatedOptionsIsAnObject()
    {
        $this->assertInstanceOf('stdClass',
            $this->opts_handler->getValidatedOptions($this->raw_options));
    }

    public function testSetsValidMailbox()
    {
        $this->raw_options['mailbox'] = "/tmp";
        $options = $this->opts_handler->getValidatedOptions($this->raw_options);
        $this->assertEquals("/tmp", $options->mailbox);
    }

    public function testThrowsExceptionOnInvalidMailbox()
    {
        try {
            $this->opts_handler->validateMailbox("/");
        } catch (\Exception $expected) {
            return true;
        }
        $this->fail("Expected exception was not raised.");
    }

    public function testSetsValidFilter()
    {
        $this->raw_options['filter'] = "/foo/";
        $options = $this->opts_handler->getValidatedOptions($this->raw_options);
        $this->assertEquals("/foo/", $options->filter);
    }

    public function testThrowsExceptionOnInvalidFilter()
    {
        try {
            $this->opts_handler->validateFilter("foo/");
        } catch (\Exception $expected) {
            return true;
        }
        $this->fail("Expected exception was not raised.");
    }

    public function testClothesNakedFilter()
    {
        $this->raw_options['filter'] = "foo";
        $options = $this->opts_handler->getValidatedOptions($this->raw_options);
        $this->assertEquals("/foo/", $options->filter);
    }

    public function testSetsValidExt()
    {
        $this->raw_options['ext'] = "eml";
        $options = $this->opts_handler->getValidatedOptions($this->raw_options);
        $this->assertEquals("eml", $options->ext);
    }

    public function testFixesInvalidExt()
    {
        $this->raw_options['ext'] = ".eml";
        $options = $this->opts_handler->getValidatedOptions($this->raw_options);
        $this->assertEquals("eml", $options->ext);
    }
}
