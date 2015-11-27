<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule5 - Select a plural form for a specified number.
 *
 * Families:
 * Romanic (Romanian)
 *
 * nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2);
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule5 implements PluralRuleInterface {
	public function plurals() {
		return 3;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number === 1) {
			return 0;
		} elseif ($number === 0 || ($number % 100 > 0 && $number % 100 < 20)) {
			return 1;
		} else {
			return 2;
		}
	}
}
