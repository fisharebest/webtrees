<?php namespace Fisharebest\Localization;

/**
 * Class Translator - use a translation to translate messages.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class Translator {
	/** @var \ArrayAccess An association of English -> translated messages */
	private $translations;

	/** @var PluralRuleInterface */
	private $plural_rule;

	/**
	 * Create a translator
	 *
	 * @param array               $translations
	 * @param PluralRuleInterface $plural_rule
	 */
	public function __construct($translations, $plural_rule) {
		$this->translations = $translations;
		$this->plural_rule  = $plural_rule;
	}

	/**
	 * Check whether a message is translated.
	 *
	 * @param string $message English text to check
	 *
	 * @return boolean
	 */
	public function isTranslated($message) {
		return isset($this->translations[$message]);
	}

	/**
	 * Translate a plural message into another language.
	 *
	 * @param string  $message1 English text for singular
	 * @param string  $message2 English text for plural
	 * @param integer $number   Number of entities
	 *
	 * @return string Translated text
	 */
	public function plural($message1, $message2, $number) {
		$key = $message1 . chr(0) . $message2;
		if (isset($this->translations[$key])) {
			$plurals = explode(chr(0), $this->translations[$key]);
			if (count($plurals) === $this->plural_rule->plurals()) {
				return $plurals[$this->plural_rule->plural($number)];
			}
		}

		return $number === 1 ? $message1 : $message2;
	}

	/**
	 * Translate a message into another language.
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
}
