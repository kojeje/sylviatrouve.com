<?php

namespace MailPoetVendor\Sabberworm\CSS;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Sabberworm\CSS\CSSList\Document;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
/**
 * Parser class parses CSS from text into a data structure.
 */
class Parser
{
    private $oParserState;
    /**
     * Parser constructor.
     * Note that that iLineNo starts from 1 and not 0
     *
     * @param $sText
     * @param Settings|null $oParserSettings
     * @param int $iLineNo
     */
    public function __construct($sText, \MailPoetVendor\Sabberworm\CSS\Settings $oParserSettings = null, $iLineNo = 1)
    {
        if ($oParserSettings === null) {
            $oParserSettings = \MailPoetVendor\Sabberworm\CSS\Settings::create();
        }
        $this->oParserState = new \MailPoetVendor\Sabberworm\CSS\Parsing\ParserState($sText, $oParserSettings, $iLineNo);
    }
    public function setCharset($sCharset)
    {
        $this->oParserState->setCharset($sCharset);
    }
    public function getCharset()
    {
        $this->oParserState->getCharset();
    }
    public function parse()
    {
        return \MailPoetVendor\Sabberworm\CSS\CSSList\Document::parse($this->oParserState);
    }
}
