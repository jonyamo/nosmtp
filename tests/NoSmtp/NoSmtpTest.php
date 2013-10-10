<?php
namespace NoSmtp;

class NoSmtpTest extends \PHPUnit_Framework_TestCase
{
    protected $nosmtp;

    protected function setUp()
    {
        $opts_handler = new CliOptionsHandler;
        $this->nosmtp = new NoSmtp($opts_handler->getValidatedOptions(
            array('mailbox'=>"/tmp",'ext'=>".eml",'filter'=>"/.*/")));
    }

    public function testGeneratesValidFileName()
    {
        $this->assertRegExp('/\/tmp\/.*\.eml/',
            $this->nosmtp->generateFilename());
    }

    public function testFilterMatchesDefaultTo()
    {
        $mail = "To: foo@bar.com";
        $this->assertTrue($this->nosmtp->recipientsMatchFilter($mail));
    }

    public function testFilterMatchesDefaultCc()
    {
        $mail = "Cc: foo@bar.com";
        $this->assertTrue($this->nosmtp->recipientsMatchFilter($mail));
    }

    public function testFilterMatchesDefaultBcc()
    {
        $mail = "Bcc: foo@bar.com";
        $this->assertTrue($this->nosmtp->recipientsMatchFilter($mail));
    }

    public function testFilterMatchesSpecific()
    {
        $opts_handler = new CliOptionsHandler;
        $nosmtp = new NoSmtp($opts_handler->getValidatedOptions(
            array('filter'=>"quux@wizbang.com")));
        $mail = "To: quux@wizbang.com";
        $this->assertTrue($nosmtp->recipientsMatchFilter($mail));
    }

    public function testFilterIgnoresInvalid()
    {
        $opts_handler = new CliOptionsHandler;
        $nosmtp = new NoSmtp($opts_handler->getValidatedOptions(
            array('filter'=>"quux@wizbang.com")));
        $mail = "To: foo@bar.com";
        $this->assertFalse($nosmtp->recipientsMatchFilter($mail));
    }
}
