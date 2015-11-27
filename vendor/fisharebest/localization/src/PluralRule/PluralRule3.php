<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule3 - Select a plural form for a specified number.
 *
 * Families:
 * Baltic (Latvian)
 *
 * nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule3 implements PluralRuleInterface {
	public function plurals() {
		return 3;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number % 10 === 1 && $number % 100 !== 11) {
			return 0;
		} elseif ($number !== 0) {
			return 1;
		} else {
			return 2;
		}
	}
}
