<?php

namespace MailPoetVendor\Egulias\EmailValidator\Validation;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Validation\Error\RFCWarnings;
class NoRFCWarningsValidation extends \MailPoetVendor\Egulias\EmailValidator\Validation\RFCValidation
{
    /**
     * @var InvalidEmail|null
     */
    private $error;
    /**
     * {@inheritdoc}
     */
    public function isValid($email, \MailPoetVendor\Egulias\EmailValidator\EmailLexer $emailLexer)
    {
        if (!parent::isValid($email, $emailLexer)) {
            return \false;
        }
        if (empty($this->getWarnings())) {
            return \true;
        }
        $this->error = new \MailPoetVendor\Egulias\EmailValidator\Validation\Error\RFCWarnings();
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error ?: parent::getError();
    }
}
