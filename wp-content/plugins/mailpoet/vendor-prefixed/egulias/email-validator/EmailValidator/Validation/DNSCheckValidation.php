<?php

namespace MailPoetVendor\Egulias\EmailValidator\Validation;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Warning\NoDNSMXRecord;
use MailPoetVendor\Egulias\EmailValidator\Exception\NoDNSRecord;
class DNSCheckValidation implements \MailPoetVendor\Egulias\EmailValidator\Validation\EmailValidation
{
    /**
     * @var array
     */
    private $warnings = [];
    /**
     * @var InvalidEmail|null
     */
    private $error;
    public function __construct()
    {
        if (!\function_exists('idn_to_ascii')) {
            throw new \LogicException(\sprintf('The %s class requires the Intl extension.', __CLASS__));
        }
    }
    public function isValid($email, \MailPoetVendor\Egulias\EmailValidator\EmailLexer $emailLexer)
    {
        // use the input to check DNS if we cannot extract something similar to a domain
        $host = $email;
        // Arguable pattern to extract the domain. Not aiming to validate the domain nor the email
        if (\false !== ($lastAtPos = \strrpos($email, '@'))) {
            $host = \substr($email, $lastAtPos + 1);
        }
        return $this->checkDNS($host);
    }
    public function getError()
    {
        return $this->error;
    }
    public function getWarnings()
    {
        return $this->warnings;
    }
    /**
     * @param string $host
     *
     * @return bool
     */
    protected function checkDNS($host)
    {
        $variant = \INTL_IDNA_VARIANT_2003;
        if (\defined('INTL_IDNA_VARIANT_UTS46')) {
            $variant = \INTL_IDNA_VARIANT_UTS46;
        }
        $host = \rtrim(\idn_to_ascii($host, \IDNA_DEFAULT, $variant), '.') . '.';
        $Aresult = \true;
        $MXresult = \checkdnsrr($host, 'MX');
        if (!$MXresult) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\NoDNSMXRecord::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\NoDNSMXRecord();
            $Aresult = \checkdnsrr($host, 'A') || \checkdnsrr($host, 'AAAA');
            if (!$Aresult) {
                $this->error = new \MailPoetVendor\Egulias\EmailValidator\Exception\NoDNSRecord();
            }
        }
        return $MXresult || $Aresult;
    }
}
