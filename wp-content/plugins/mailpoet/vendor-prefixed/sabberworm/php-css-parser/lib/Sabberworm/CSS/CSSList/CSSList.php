<?php

namespace MailPoetVendor\Sabberworm\CSS\CSSList;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Sabberworm\CSS\Comment\Commentable;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
use MailPoetVendor\Sabberworm\CSS\Parsing\SourceException;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException;
use MailPoetVendor\Sabberworm\CSS\Property\AtRule;
use MailPoetVendor\Sabberworm\CSS\Property\Charset;
use MailPoetVendor\Sabberworm\CSS\Property\CSSNamespace;
use MailPoetVendor\Sabberworm\CSS\Property\Import;
use MailPoetVendor\Sabberworm\CSS\Property\Selector;
use MailPoetVendor\Sabberworm\CSS\Renderable;
use MailPoetVendor\Sabberworm\CSS\RuleSet\AtRuleSet;
use MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock;
use MailPoetVendor\Sabberworm\CSS\RuleSet\RuleSet;
use MailPoetVendor\Sabberworm\CSS\Value\CSSString;
use MailPoetVendor\Sabberworm\CSS\Value\URL;
use MailPoetVendor\Sabberworm\CSS\Value\Value;
/**
 * A CSSList is the most generic container available. Its contents include RuleSet as well as other CSSList objects.
 * Also, it may contain Import and Charset objects stemming from @-rules.
 */
abstract class CSSList implements \MailPoetVendor\Sabberworm\CSS\Renderable, \MailPoetVendor\Sabberworm\CSS\Comment\Commentable
{
    protected $aComments;
    protected $aContents;
    protected $iLineNo;
    public function __construct($iLineNo = 0)
    {
        $this->aComments = array();
        $this->aContents = array();
        $this->iLineNo = $iLineNo;
    }
    public static function parseList(\MailPoetVendor\Sabberworm\CSS\Parsing\ParserState $oParserState, \MailPoetVendor\Sabberworm\CSS\CSSList\CSSList $oList)
    {
        $bIsRoot = $oList instanceof \MailPoetVendor\Sabberworm\CSS\CSSList\Document;
        if (\is_string($oParserState)) {
            $oParserState = new \MailPoetVendor\Sabberworm\CSS\Parsing\ParserState($oParserState);
        }
        $bLenientParsing = $oParserState->getSettings()->bLenientParsing;
        while (!$oParserState->isEnd()) {
            $comments = $oParserState->consumeWhiteSpace();
            $oListItem = null;
            if ($bLenientParsing) {
                try {
                    $oListItem = self::parseListItem($oParserState, $oList);
                } catch (\MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException $e) {
                    $oListItem = \false;
                }
            } else {
                $oListItem = self::parseListItem($oParserState, $oList);
            }
            if ($oListItem === null) {
                // List parsing finished
                return;
            }
            if ($oListItem) {
                $oListItem->setComments($comments);
                $oList->append($oListItem);
            }
            $oParserState->consumeWhiteSpace();
        }
        if (!$bIsRoot && !$bLenientParsing) {
            throw new \MailPoetVendor\Sabberworm\CSS\Parsing\SourceException("Unexpected end of document", $oParserState->currentLine());
        }
    }
    private static function parseListItem(\MailPoetVendor\Sabberworm\CSS\Parsing\ParserState $oParserState, \MailPoetVendor\Sabberworm\CSS\CSSList\CSSList $oList)
    {
        $bIsRoot = $oList instanceof \MailPoetVendor\Sabberworm\CSS\CSSList\Document;
        if ($oParserState->comes('@')) {
            $oAtRule = self::parseAtRule($oParserState);
            if ($oAtRule instanceof \MailPoetVendor\Sabberworm\CSS\Property\Charset) {
                if (!$bIsRoot) {
                    throw new \MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException('@charset may only occur in root document', '', 'custom', $oParserState->currentLine());
                }
                if (\count($oList->getContents()) > 0) {
                    throw new \MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException('@charset must be the first parseable token in a document', '', 'custom', $oParserState->currentLine());
                }
                $oParserState->setCharset($oAtRule->getCharset()->getString());
            }
            return $oAtRule;
        } else {
            if ($oParserState->comes('}')) {
                $oParserState->consume('}');
                if ($bIsRoot) {
                    if ($oParserState->getSettings()->bLenientParsing) {
                        while ($oParserState->comes('}')) {
                            $oParserState->consume('}');
                        }
                        return \MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock::parse($oParserState);
                    } else {
                        throw new \MailPoetVendor\Sabberworm\CSS\Parsing\SourceException("Unopened {", $oParserState->currentLine());
                    }
                } else {
                    return null;
                }
            } else {
                return \MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock::parse($oParserState);
            }
        }
    }
    private static function parseAtRule(\MailPoetVendor\Sabberworm\CSS\Parsing\ParserState $oParserState)
    {
        $oParserState->consume('@');
        $sIdentifier = $oParserState->parseIdentifier();
        $iIdentifierLineNum = $oParserState->currentLine();
        $oParserState->consumeWhiteSpace();
        if ($sIdentifier === 'import') {
            $oLocation = \MailPoetVendor\Sabberworm\CSS\Value\URL::parse($oParserState);
            $oParserState->consumeWhiteSpace();
            $sMediaQuery = null;
            if (!$oParserState->comes(';')) {
                $sMediaQuery = $oParserState->consumeUntil(';');
            }
            $oParserState->consume(';');
            return new \MailPoetVendor\Sabberworm\CSS\Property\Import($oLocation, $sMediaQuery, $iIdentifierLineNum);
        } else {
            if ($sIdentifier === 'charset') {
                $sCharset = \MailPoetVendor\Sabberworm\CSS\Value\CSSString::parse($oParserState);
                $oParserState->consumeWhiteSpace();
                $oParserState->consume(';');
                return new \MailPoetVendor\Sabberworm\CSS\Property\Charset($sCharset, $iIdentifierLineNum);
            } else {
                if (self::identifierIs($sIdentifier, 'keyframes')) {
                    $oResult = new \MailPoetVendor\Sabberworm\CSS\CSSList\KeyFrame($iIdentifierLineNum);
                    $oResult->setVendorKeyFrame($sIdentifier);
                    $oResult->setAnimationName(\trim($oParserState->consumeUntil('{', \false, \true)));
                    \MailPoetVendor\Sabberworm\CSS\CSSList\CSSList::parseList($oParserState, $oResult);
                    return $oResult;
                } else {
                    if ($sIdentifier === 'namespace') {
                        $sPrefix = null;
                        $mUrl = \MailPoetVendor\Sabberworm\CSS\Value\Value::parsePrimitiveValue($oParserState);
                        if (!$oParserState->comes(';')) {
                            $sPrefix = $mUrl;
                            $mUrl = \MailPoetVendor\Sabberworm\CSS\Value\Value::parsePrimitiveValue($oParserState);
                        }
                        $oParserState->consume(';');
                        if ($sPrefix !== null && !\is_string($sPrefix)) {
                            throw new \MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException('Wrong namespace prefix', $sPrefix, 'custom', $iIdentifierLineNum);
                        }
                        if (!($mUrl instanceof \MailPoetVendor\Sabberworm\CSS\Value\CSSString || $mUrl instanceof \MailPoetVendor\Sabberworm\CSS\Value\URL)) {
                            throw new \MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException('Wrong namespace url of invalid type', $mUrl, 'custom', $iIdentifierLineNum);
                        }
                        return new \MailPoetVendor\Sabberworm\CSS\Property\CSSNamespace($mUrl, $sPrefix, $iIdentifierLineNum);
                    } else {
                        //Unknown other at rule (font-face or such)
                        $sArgs = \trim($oParserState->consumeUntil('{', \false, \true));
                        if (\substr_count($sArgs, "(") != \substr_count($sArgs, ")")) {
                            if ($oParserState->getSettings()->bLenientParsing) {
                                return NULL;
                            } else {
                                throw new \MailPoetVendor\Sabberworm\CSS\Parsing\SourceException("Unmatched brace count in media query", $oParserState->currentLine());
                            }
                        }
                        $bUseRuleSet = \true;
                        foreach (\explode('/', \MailPoetVendor\Sabberworm\CSS\Property\AtRule::BLOCK_RULES) as $sBlockRuleName) {
                            if (self::identifierIs($sIdentifier, $sBlockRuleName)) {
                                $bUseRuleSet = \false;
                                break;
                            }
                        }
                        if ($bUseRuleSet) {
                            $oAtRule = new \MailPoetVendor\Sabberworm\CSS\RuleSet\AtRuleSet($sIdentifier, $sArgs, $iIdentifierLineNum);
                            \MailPoetVendor\Sabberworm\CSS\RuleSet\RuleSet::parseRuleSet($oParserState, $oAtRule);
                        } else {
                            $oAtRule = new \MailPoetVendor\Sabberworm\CSS\CSSList\AtRuleBlockList($sIdentifier, $sArgs, $iIdentifierLineNum);
                            \MailPoetVendor\Sabberworm\CSS\CSSList\CSSList::parseList($oParserState, $oAtRule);
                        }
                        return $oAtRule;
                    }
                }
            }
        }
    }
    /**
     * Tests an identifier for a given value. Since identifiers are all keywords, they can be vendor-prefixed. We need to check for these versions too.
     */
    private static function identifierIs($sIdentifier, $sMatch)
    {
        return \strcasecmp($sIdentifier, $sMatch) === 0 ?: \preg_match("/^(-\\w+-)?{$sMatch}\$/i", $sIdentifier) === 1;
    }
    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;
    }
    /**
     * Prepend item to list of contents.
     *
     * @param object $oItem Item.
     */
    public function prepend($oItem)
    {
        \array_unshift($this->aContents, $oItem);
    }
    /**
     * Append item to list of contents.
     *
     * @param object $oItem Item.
     */
    public function append($oItem)
    {
        $this->aContents[] = $oItem;
    }
    /**
     * Splice the list of contents.
     *
     * @param int       $iOffset      Offset.
     * @param int       $iLength      Length. Optional.
     * @param RuleSet[] $mReplacement Replacement. Optional.
     */
    public function splice($iOffset, $iLength = null, $mReplacement = null)
    {
        \array_splice($this->aContents, $iOffset, $iLength, $mReplacement);
    }
    /**
     * Removes an item from the CSS list.
     * @param RuleSet|Import|Charset|CSSList $oItemToRemove May be a RuleSet (most likely a DeclarationBlock), a Import, a Charset or another CSSList (most likely a MediaQuery)
     * @return bool Whether the item was removed.
     */
    public function remove($oItemToRemove)
    {
        $iKey = \array_search($oItemToRemove, $this->aContents, \true);
        if ($iKey !== \false) {
            unset($this->aContents[$iKey]);
            return \true;
        }
        return \false;
    }
    /**
     * Replaces an item from the CSS list.
     * @param RuleSet|Import|Charset|CSSList $oItemToRemove May be a RuleSet (most likely a DeclarationBlock), a Import, a Charset or another CSSList (most likely a MediaQuery)
     */
    public function replace($oOldItem, $oNewItem)
    {
        $iKey = \array_search($oOldItem, $this->aContents, \true);
        if ($iKey !== \false) {
            \array_splice($this->aContents, $iKey, 1, $oNewItem);
            return \true;
        }
        return \false;
    }
    /**
     * Set the contents.
     * @param array $aContents Objects to set as content.
     */
    public function setContents(array $aContents)
    {
        $this->aContents = array();
        foreach ($aContents as $content) {
            $this->append($content);
        }
    }
    /**
     * Removes a declaration block from the CSS list if it matches all given selectors.
     * @param array|string $mSelector The selectors to match.
     * @param boolean $bRemoveAll Whether to stop at the first declaration block found or remove all blocks
     */
    public function removeDeclarationBlockBySelector($mSelector, $bRemoveAll = \false)
    {
        if ($mSelector instanceof \MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock) {
            $mSelector = $mSelector->getSelectors();
        }
        if (!\is_array($mSelector)) {
            $mSelector = \explode(',', $mSelector);
        }
        foreach ($mSelector as $iKey => &$mSel) {
            if (!$mSel instanceof \MailPoetVendor\Sabberworm\CSS\Property\Selector) {
                $mSel = new \MailPoetVendor\Sabberworm\CSS\Property\Selector($mSel);
            }
        }
        foreach ($this->aContents as $iKey => $mItem) {
            if (!$mItem instanceof \MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock) {
                continue;
            }
            if ($mItem->getSelectors() == $mSelector) {
                unset($this->aContents[$iKey]);
                if (!$bRemoveAll) {
                    return;
                }
            }
        }
    }
    public function __toString()
    {
        return $this->render(new \MailPoetVendor\Sabberworm\CSS\OutputFormat());
    }
    public function render(\MailPoetVendor\Sabberworm\CSS\OutputFormat $oOutputFormat)
    {
        $sResult = '';
        $bIsFirst = \true;
        $oNextLevel = $oOutputFormat;
        if (!$this->isRootList()) {
            $oNextLevel = $oOutputFormat->nextLevel();
        }
        foreach ($this->aContents as $oContent) {
            $sRendered = $oOutputFormat->safely(function () use($oNextLevel, $oContent) {
                return $oContent->render($oNextLevel);
            });
            if ($sRendered === null) {
                continue;
            }
            if ($bIsFirst) {
                $bIsFirst = \false;
                $sResult .= $oNextLevel->spaceBeforeBlocks();
            } else {
                $sResult .= $oNextLevel->spaceBetweenBlocks();
            }
            $sResult .= $sRendered;
        }
        if (!$bIsFirst) {
            // Had some output
            $sResult .= $oOutputFormat->spaceAfterBlocks();
        }
        return $sResult;
    }
    /**
     * Return true if the list can not be further outdented. Only important when rendering.
     */
    public abstract function isRootList();
    public function getContents()
    {
        return $this->aContents;
    }
    /**
     * @param array $aComments Array of comments.
     */
    public function addComments(array $aComments)
    {
        $this->aComments = \array_merge($this->aComments, $aComments);
    }
    /**
     * @return array
     */
    public function getComments()
    {
        return $this->aComments;
    }
    /**
     * @param array $aComments Array containing Comment objects.
     */
    public function setComments(array $aComments)
    {
        $this->aComments = $aComments;
    }
}
