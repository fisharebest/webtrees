<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule8 - Select a plural form for a specified number.
 *
 * Families:
 * Slavic (Slovak, Czech)
 *
 * nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule8 implements PluralRuleInterface {
	public function plurals() {
		return 3;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number === 1) {
			return 0;
		} elseif ($number >= 2 && $number <= 4) {
			return 1;
		} else {
			return 2;
		}
	}
}
