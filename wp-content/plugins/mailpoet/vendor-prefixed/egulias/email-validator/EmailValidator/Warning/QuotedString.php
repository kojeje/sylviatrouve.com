<?php

namespace MailPoetVendor\Egulias\EmailValidator\Warning;

if (!defined('ABSPATH')) exit;


class QuotedString extends \MailPoetVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 11;
    /**
     * @param scalar $prevToken
     * @param scalar $postToken
     */
    public function __construct($prevToken, $postToken)
    {
        $this->message = "Quoted String found between {$prevToken} and {$postToken}";
    }
}
