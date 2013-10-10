<?php
namespace NoSmtp;

class CliOptionsHandler
{
    const DEFAULT_MAILBOX = "/tmp";
    const DEFAULT_EXT = "eml";
    const DEFAULT_FILTER = "/.*/";

    public function getRawOptions()
    {
        return getopt(
            "h".
            "V",
            array(
                "help",
                "version",
                "mailbox:",
                "ext:",
                "filter:"
            )
        );
    }

    public function handleExitOptions($raw_options)
    {
        # help
        if (isset($raw_options['h']) || isset($raw_options['help'])) {
            fwrite( STDOUT, self::_getUsage() . PHP_EOL );
            exit(0);
        }

        # version
        if (isset($raw_options['V']) || isset($raw_options['version'])) {
            fwrite( STDOUT, NoSmtp::VERSION . PHP_EOL );
            exit(0);
        }
    }

    public function getValidatedOptions($raw_options=array())
    {
        $options = new \stdClass;

        # mailbox
        try {
            $mailbox = @$raw_options['mailbox'] ?: self::DEFAULT_MAILBOX;
            $options->mailbox = $this->validateMailbox($mailbox);
        } catch (\Exception $e) {
            fwrite( STDERR, sprintf("ERROR: %s", $e->getMessage()) . PHP_EOL );
            exit(1);
        }

        # filter
        try {
            $filter = @$raw_options['filter'] ?: self::DEFAULT_FILTER;
            $options->filter = $this->validateFilter($filter);
        } catch (\Exception $e) {
            fwrite( STDERR, sprintf("ERROR: %s", $e->getMessage()) . PHP_EOL );
            exit(1);
        }

        # ext
        $ext = @$raw_options['ext'] ?: self::DEFAULT_EXT;
        $options->ext = $this->validateExt($ext);

        return $options;
    }

    public function validateMailbox($mailbox)
    {
        if (!is_writeable($mailbox)) {
            throw new \Exception("$mailbox is not writeable.");
        }
        return $mailbox;
    }

    public function validateExt($ext)
    {
        if (strpos($ext, ".") === 0) {
            $ext = substr($ext,1);
        }
        return $ext;
    }

    public function validateFilter($filter)
    {
        if (strpos($filter, "/") !== 0) {
            $filter = "/{$filter}/";
        }
        if (@preg_match($filter,'') === false) {
            throw new \Exception("$filter is not a valid regex.");
        }
        return $filter;
    }

    private static function _getUsage()
    {
        return <<<EOM
Usage: nosmtp [options]

Specific options:
   --mailbox DIR   directory where the file will be saved. [default: /tmp]
   --ext     EXT   extension to be used for the file. [default: eml]
   --filter  RGXP  address filter. will only save emails sent to recipients
                   matching the given pattern. [default: .*]

Common options:
   -h, --help          show this message
   -V, --version       show version
EOM;
    }
}
