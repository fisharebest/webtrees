<?php

namespace Fisharebest\Localization;

use Fisharebest\Localization\PluralRule\PluralRuleInterface;

/**
 * Class Translator - use a translation to translate messages.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class Translator
{
    /** @var array<string,string> An association of English -> translated messages */
    private $translations;

    /** @var PluralRuleInterface */
    private $plural_rule;

    /**
     * Create a translator
     *
     * @param array<string,string> $translations
     * @param PluralRuleInterface  $plural_rule
     */
    public function __construct(array $translations, $plural_rule)
    {
        $this->translations = $translations;
        $this->plural_rule  = $plural_rule;
    }

    /**
     * Translate a message into another language.
     * Works the same as gettext().
     *
     * @param string $message English text to translate
     *
     * @return string Translated text
     */
    public function translate($message)
    {
        if (isset($this->translations[$message])) {
            return $this->translations[$message];
        }

        return $message;
    }

    /**
     * Translate a context-sensitive message into another language.
     * Works the same as pgettext().
     *
     * @param string $context Context of the message, e.g. "verb" or "noun"
     * @param string $message English text to translate
     *
     * @return string Translated text
     */
    public function translateContext($context, $message)
    {
        $key = $context . Translation::CONTEXT_SEPARATOR . $message;
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }

        return $message;
    }

    /**
     * Translate a plural message into another language.
     * Works the same as ngettext().
     *
     * @param string $message1 English text for singular
     * @param string $message2 English text for plural
     * @param int    $number   Number of entities
     *
     * @return string Translated text
     */
    public function translatePlural($message1, $message2, $number)
    {
        $key = $message1 . Translation::PLURAL_SEPARATOR . $message2;
        if (isset($this->translations[$key])) {
            $plurals = explode(Translation::PLURAL_SEPARATOR, $this->translations[$key]);
            if (count($plurals) === $this->plural_rule->plurals()) {
                return $plurals[$this->plural_rule->plural($number)];
            }
        }

        return $number === 1 ? $message1 : $message2;
    }
}
