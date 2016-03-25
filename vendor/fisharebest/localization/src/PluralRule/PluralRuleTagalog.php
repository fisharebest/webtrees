<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRuleTagalog - Select a plural form for a specified number.
 *
 * nplurals=2; plural=n % 10 != 4 && n%10 != 6 && n%10 != 9;
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRuleTagalog implements PluralRuleInterface {
	public function plurals() {
		return 2;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number % 10 === 4 || $number % 10 === 6 || $number % 10 === 9) {
			return 1;
		} else {
			return 0;
		}
	}
}
