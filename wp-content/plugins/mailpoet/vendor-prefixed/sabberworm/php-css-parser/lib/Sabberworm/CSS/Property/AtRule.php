<?php

namespace MailPoetVendor\Sabberworm\CSS\Property;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Sabberworm\CSS\Renderable;
use MailPoetVendor\Sabberworm\CSS\Comment\Commentable;
interface AtRule extends \MailPoetVendor\Sabberworm\CSS\Renderable, \MailPoetVendor\Sabberworm\CSS\Comment\Commentable
{
    // Since there are more set rules than block rules, we’re whitelisting the block rules and have anything else be treated as a set rule.
    const BLOCK_RULES = 'media/document/supports/region-style/font-feature-values';
    // …and more font-specific ones (to be used inside font-feature-values)
    const SET_RULES = 'font-face/counter-style/page/swash/styleset/annotation';
    public function atRuleName();
    public function atRuleArgs();
}
