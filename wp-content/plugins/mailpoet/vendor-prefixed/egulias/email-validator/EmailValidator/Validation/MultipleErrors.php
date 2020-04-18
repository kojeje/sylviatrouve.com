<?php

namespace MailPoetVendor\Egulias\EmailValidator\Validation;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Egulias\EmailValidator\Exception\InvalidEmail;
class MultipleErrors extends \MailPoetVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 999;
    const REASON = "Accumulated errors for multiple validations";
    /**
     * @var InvalidEmail[]
     */
    private $errors = [];
    /**
     * @param InvalidEmail[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct();
    }
    /**
     * @return InvalidEmail[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
