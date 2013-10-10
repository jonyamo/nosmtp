<?php
namespace NoSmtp;

class NoSmtp
{
    const VERSION = "0.0.1";
    const RECIPIENT_REGEX = "/^To:|^Cc:|^Bcc:/";

    private $_options;

    public function __construct($options)
    {
        $this->_options = $options;
    }

    public function generateFilename()
    {
        list($microseconds, $timestamp) = explode(" ", microtime());
        return "{$this->_options->mailbox}/"
            .  "{$timestamp}.{$microseconds}.{$this->_options->ext}";
    }

    public function recipientsMatchFilter($mail)
    {
        foreach (explode(PHP_EOL, $mail) as $line) {
            if (preg_match(self::RECIPIENT_REGEX, $line)
                && preg_match($this->_options->filter, $line)
            ) {
                return true;
            }
        }
        return false;
    }

    public function save($mail)
    {
        $filename = $this->generateFilename();
        if (!$fh = fopen($filename, "w+")) {
            throw new \Exception("unable to create $filename");
            return false;
        }
        if (fwrite($fh, $mail) === false) {
            throw new \Exception("unable to write to $filename");
        }
        fclose($fh);
    }

    public function run()
    {
        $mail = $this->_getMailFromStdin();
        if ($this->recipientsMatchFilter($mail)) {
            try {
                $this->save($mail);
            } catch (\Exception $e) {
                fwrite( STDERR, sprintf("ERROR: %s", $e->getMessage()) . PHP_EOL );
                exit(1);
            }
        }
    }

    private function _getMailFromStdin()
    {
        $mail = "";
        $in = fopen("php://stdin", "r");
        while (!feof($in)) {
            $mail .= fgets($in, 4096);
        }
        return $mail;
    }
}
