<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule1 - Select a plural form for a specified number.
 *
 * Families:
 * Germanic (Danish, Dutch, English, Faroese, Frisian, German, Norwegian, Swedish)
 * Finno-Ugric (Estonian, Finnish, Hungarian)
 * AbstractLanguage isolate (Basque)
 * Latin/Greek (Greek)
 * Semitic (Hebrew)
 * Romanic (Italian, Portuguese, Spanish, Catalan)
 *
 * nplurals=2; plural=(n != 1);
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule1 implements PluralRuleInterface {
	public function plurals() {
		return 2;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number === 1) {
			return 0;
		} else {
			return 1;
		}
	}
}
