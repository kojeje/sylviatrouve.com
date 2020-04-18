<?php

namespace MailPoetVendor\Egulias\EmailValidator\Validation;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Warning\Warning;
interface EmailValidation
{
    /**
     * Returns true if the given email is valid.
     *
     * @param string     $email      The email you want to validate.
     * @param EmailLexer $emailLexer The email lexer.
     *
     * @return bool
     */
    public function isValid($email, \MailPoetVendor\Egulias\EmailValidator\EmailLexer $emailLexer);
    /**
     * Returns the validation error.
     *
     * @return InvalidEmail|null
     */
    public function getError();
    /**
     * Returns the validation warnings.
     *
     * @return Warning[]
     */
    public function getWarnings();
}
