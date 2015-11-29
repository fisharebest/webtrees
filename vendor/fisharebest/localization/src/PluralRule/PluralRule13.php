<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule13 - Select a plural form for a specified number.
 *
 * Families:
 * Semitic (Maltese)
 *
 * nplurals=4; plural=(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3);
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule13 implements PluralRuleInterface {
	public function plurals() {
		return 4;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number === 1) {
			return 0;
		} elseif ($number === 0 || ($number % 100 > 1 && $number % 100 < 11)) {
			return 1;
		} elseif ($number % 100 > 10 && $number % 100 < 20) {
			return 2;
		} else {
			return 3;
		}
	}
}
