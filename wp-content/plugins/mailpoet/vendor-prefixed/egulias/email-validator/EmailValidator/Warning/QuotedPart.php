<?php

namespace MailPoetVendor\Egulias\EmailValidator\Warning;

if (!defined('ABSPATH')) exit;


class QuotedPart extends \MailPoetVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 36;
    /**
     * @param scalar $prevToken
     * @param scalar $postToken
     */
    public function __construct($prevToken, $postToken)
    {
        $this->message = "Deprecated Quoted String found between {$prevToken} and {$postToken}";
    }
}
