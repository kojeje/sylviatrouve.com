<?php

namespace MailPoetVendor\Egulias\EmailValidator\Parser;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Exception\CharNotAllowed;
use MailPoetVendor\Egulias\EmailValidator\Exception\CommaInDomain;
use MailPoetVendor\Egulias\EmailValidator\Exception\ConsecutiveAt;
use MailPoetVendor\Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use MailPoetVendor\Egulias\EmailValidator\Exception\CRNoLF;
use MailPoetVendor\Egulias\EmailValidator\Exception\DomainHyphened;
use MailPoetVendor\Egulias\EmailValidator\Exception\DotAtEnd;
use MailPoetVendor\Egulias\EmailValidator\Exception\DotAtStart;
use MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingATEXT;
use MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingDomainLiteralClose;
use MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingDTEXT;
use MailPoetVendor\Egulias\EmailValidator\Exception\NoDomainPart;
use MailPoetVendor\Egulias\EmailValidator\Exception\UnopenedComment;
use MailPoetVendor\Egulias\EmailValidator\Warning\AddressLiteral;
use MailPoetVendor\Egulias\EmailValidator\Warning\CFWSWithFWS;
use MailPoetVendor\Egulias\EmailValidator\Warning\DeprecatedComment;
use MailPoetVendor\Egulias\EmailValidator\Warning\DomainLiteral;
use MailPoetVendor\Egulias\EmailValidator\Warning\DomainTooLong;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6BadChar;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonEnd;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonStart;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6Deprecated;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6DoubleColon;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6GroupCount;
use MailPoetVendor\Egulias\EmailValidator\Warning\IPV6MaxGroups;
use MailPoetVendor\Egulias\EmailValidator\Warning\LabelTooLong;
use MailPoetVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT;
use MailPoetVendor\Egulias\EmailValidator\Warning\TLD;
class DomainPart extends \MailPoetVendor\Egulias\EmailValidator\Parser\Parser
{
    const DOMAIN_MAX_LENGTH = 254;
    /**
     * @var string
     */
    protected $domainPart = '';
    public function parse($domainPart)
    {
        $this->lexer->moveNext();
        $this->performDomainStartChecks();
        $domain = $this->doParseDomainPart();
        $prev = $this->lexer->getPrevious();
        $length = \strlen($domain);
        if ($prev['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DOT) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\DotAtEnd();
        }
        if ($prev['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_HYPHEN) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\DomainHyphened();
        }
        if ($length > self::DOMAIN_MAX_LENGTH) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\DomainTooLong::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\DomainTooLong();
        }
        if ($prev['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CR) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\CRLFAtTheEnd();
        }
        $this->domainPart = $domain;
    }
    private function performDomainStartChecks()
    {
        $this->checkInvalidTokensAfterAT();
        $this->checkEmptyDomain();
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_OPENPARENTHESIS) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\DeprecatedComment::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\DeprecatedComment();
            $this->parseDomainComments();
        }
    }
    private function checkEmptyDomain()
    {
        $thereIsNoDomain = $this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_EMPTY || $this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_SP && !$this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::GENERIC);
        if ($thereIsNoDomain) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\NoDomainPart();
        }
    }
    private function checkInvalidTokensAfterAT()
    {
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DOT) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\DotAtStart();
        }
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_HYPHEN) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\DomainHyphened();
        }
    }
    /**
     * @return string
     */
    public function getDomainPart()
    {
        return $this->domainPart;
    }
    /**
     * @param string $addressLiteral
     * @param int $maxGroups
     */
    public function checkIPV6Tag($addressLiteral, $maxGroups = 8)
    {
        $prev = $this->lexer->getPrevious();
        if ($prev['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_COLON) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonEnd::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonEnd();
        }
        $IPv6 = \substr($addressLiteral, 5);
        //Daniel Marschall's new IPv6 testing strategy
        $matchesIP = \explode(':', $IPv6);
        $groupCount = \count($matchesIP);
        $colons = \strpos($IPv6, '::');
        if (\count(\preg_grep('/^[0-9A-Fa-f]{0,4}$/', $matchesIP, \PREG_GREP_INVERT)) !== 0) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6BadChar::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6BadChar();
        }
        if ($colons === \false) {
            // We need exactly the right number of groups
            if ($groupCount !== $maxGroups) {
                $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6GroupCount::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6GroupCount();
            }
            return;
        }
        if ($colons !== \strrpos($IPv6, '::')) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6DoubleColon::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6DoubleColon();
            return;
        }
        if ($colons === 0 || $colons === \strlen($IPv6) - 2) {
            // RFC 4291 allows :: at the start or end of an address
            //with 7 other groups in addition
            ++$maxGroups;
        }
        if ($groupCount > $maxGroups) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6MaxGroups::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6MaxGroups();
        } elseif ($groupCount === $maxGroups) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6Deprecated::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6Deprecated();
        }
    }
    /**
     * @return string
     */
    protected function doParseDomainPart()
    {
        $domain = '';
        $openedParenthesis = 0;
        do {
            $prev = $this->lexer->getPrevious();
            $this->checkNotAllowedChars($this->lexer->token);
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_OPENPARENTHESIS) {
                $this->parseComments();
                $openedParenthesis += $this->getOpenedParenthesis();
                $this->lexer->moveNext();
                $tmpPrev = $this->lexer->getPrevious();
                if ($tmpPrev['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS) {
                    $openedParenthesis--;
                }
            }
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS) {
                if ($openedParenthesis === 0) {
                    throw new \MailPoetVendor\Egulias\EmailValidator\Exception\UnopenedComment();
                } else {
                    $openedParenthesis--;
                }
            }
            $this->checkConsecutiveDots();
            $this->checkDomainPartExceptions($prev);
            if ($this->hasBrackets()) {
                $this->parseDomainLiteral();
            }
            $this->checkLabelLength($prev);
            if ($this->isFWS()) {
                $this->parseFWS();
            }
            $domain .= $this->lexer->token['value'];
            $this->lexer->moveNext();
        } while (null !== $this->lexer->token['type']);
        return $domain;
    }
    private function checkNotAllowedChars(array $token)
    {
        $notAllowed = [\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH => \true, \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_SLASH => \true];
        if (isset($notAllowed[$token['type']])) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\CharNotAllowed();
        }
    }
    /**
     * @return string|false
     */
    protected function parseDomainLiteral()
    {
        if ($this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_COLON)) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonStart::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonStart();
        }
        if ($this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_IPV6TAG)) {
            $lexer = clone $this->lexer;
            $lexer->moveNext();
            if ($lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DOUBLECOLON)) {
                $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonStart::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\IPV6ColonStart();
            }
        }
        return $this->doParseDomainLiteral();
    }
    /**
     * @return string|false
     */
    protected function doParseDomainLiteral()
    {
        $IPv6TAG = \false;
        $addressLiteral = '';
        do {
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::C_NUL) {
                throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingDTEXT();
            }
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::INVALID || $this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::C_DEL || $this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_LF) {
                $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT();
            }
            if ($this->lexer->isNextTokenAny(array(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_OPENQBRACKET, \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_OPENBRACKET))) {
                throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingDTEXT();
            }
            if ($this->lexer->isNextTokenAny(array(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_HTAB, \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_SP, $this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::CRLF))) {
                $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\CFWSWithFWS::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\CFWSWithFWS();
                $this->parseFWS();
            }
            if ($this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CR)) {
                throw new \MailPoetVendor\Egulias\EmailValidator\Exception\CRNoLF();
            }
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH) {
                $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT();
                $addressLiteral .= $this->lexer->token['value'];
                $this->lexer->moveNext();
                $this->validateQuotedPair();
            }
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_IPV6TAG) {
                $IPv6TAG = \true;
            }
            if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEQBRACKET) {
                break;
            }
            $addressLiteral .= $this->lexer->token['value'];
        } while ($this->lexer->moveNext());
        $addressLiteral = \str_replace('[', '', $addressLiteral);
        $addressLiteral = $this->checkIPV4Tag($addressLiteral);
        if (\false === $addressLiteral) {
            return $addressLiteral;
        }
        if (!$IPv6TAG) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\DomainLiteral::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\DomainLiteral();
            return $addressLiteral;
        }
        $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\AddressLiteral::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\AddressLiteral();
        $this->checkIPV6Tag($addressLiteral);
        return $addressLiteral;
    }
    /**
     * @param string $addressLiteral
     *
     * @return string|false
     */
    protected function checkIPV4Tag($addressLiteral)
    {
        $matchesIP = array();
        // Extract IPv4 part from the end of the address-literal (if there is one)
        if (\preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressLiteral, $matchesIP) > 0) {
            $index = \strrpos($addressLiteral, $matchesIP[0]);
            if ($index === 0) {
                $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\AddressLiteral::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\AddressLiteral();
                return \false;
            }
            // Convert IPv4 part to IPv6 format for further testing
            $addressLiteral = \substr($addressLiteral, 0, (int) $index) . '0:0';
        }
        return $addressLiteral;
    }
    protected function checkDomainPartExceptions(array $prev)
    {
        $invalidDomainTokens = array(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DQUOTE => \true, \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_SEMICOLON => \true, \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_GREATERTHAN => \true, \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_LOWERTHAN => \true);
        if (isset($invalidDomainTokens[$this->lexer->token['type']])) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_COMMA) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\CommaInDomain();
        }
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ConsecutiveAt();
        }
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_OPENQBRACKET && $prev['type'] !== \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_HYPHEN && $this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DOT)) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\DomainHyphened();
        }
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH && $this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::GENERIC)) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
    }
    /**
     * @return bool
     */
    protected function hasBrackets()
    {
        if ($this->lexer->token['type'] !== \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_OPENBRACKET) {
            return \false;
        }
        try {
            $this->lexer->find(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEBRACKET);
        } catch (\RuntimeException $e) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingDomainLiteralClose();
        }
        return \true;
    }
    protected function checkLabelLength(array $prev)
    {
        if ($this->lexer->token['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DOT && $prev['type'] === \MailPoetVendor\Egulias\EmailValidator\EmailLexer::GENERIC && \strlen($prev['value']) > 63) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\LabelTooLong::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\LabelTooLong();
        }
    }
    protected function parseDomainComments()
    {
        $this->isUnclosedComment();
        while (!$this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS)) {
            $this->warnEscaping();
            $this->lexer->moveNext();
        }
        $this->lexer->moveNext();
        if ($this->lexer->isNextToken(\MailPoetVendor\Egulias\EmailValidator\EmailLexer::S_DOT)) {
            throw new \MailPoetVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
    }
    protected function addTLDWarnings()
    {
        if ($this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\DomainLiteral::CODE]) {
            $this->warnings[\MailPoetVendor\Egulias\EmailValidator\Warning\TLD::CODE] = new \MailPoetVendor\Egulias\EmailValidator\Warning\TLD();
        }
    }
}
