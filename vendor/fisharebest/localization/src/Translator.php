<?php namespace Fisharebest\Localization;

use Fisharebest\Localization\PluralRule\PluralRuleInterface;

/**
 * Class Translator - use a translation to translate messages.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class Translator {
	/** @var array An association of English -> translated messages */
	private $translations;

	/** @var PluralRuleInterface */
	private $plural_rule;

	/**
	 * Create a translator
	 *
	 * @param array               $translations
	 * @param PluralRuleInterface $plural_rule
	 */
	public function __construct(array $translations, $plural_rule) {
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
	public function translate($message) {
		if (isset($this->translations[$message])) {
			return $this->translations[$message];
		} else {
			return $message;
		}
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
	public function translateContext($context, $message) {
		$key = $context . chr(4) . $message;
		if (isset($this->translations[$key])) {
			return $this->translations[$key];
		} else {
			return $message;
		}
	}

	/**
	 * Translate a plural message into another language.
	 * Works the same as ngettext().
	 *
	 * @param string  $message1 English text for singular
	 * @param string  $message2 English text for plural
	 * @param integer $number   Number of entities
	 *
	 * @return string Translated text
	 */
	public function translatePlural($message1, $message2, $number) {
		$key = $message1 . chr(0) . $message2;
		if (isset($this->translations[$key])) {
			$plurals = explode(chr(0), $this->translations[$key]);
			if (count($plurals) === $this->plural_rule->plurals()) {
				return $plurals[$this->plural_rule->plural($number)];
			}
		}

		return $number === 1 ? $message1 : $message2;
	}
}
