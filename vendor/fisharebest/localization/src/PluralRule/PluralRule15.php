<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule15 - Select a plural form for a specified number.
 *
 * Families:
 * Icelandic
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule15 implements PluralRuleInterface {
	public function plurals() {
		return 2;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number % 10 !== 1 || $number % 100 === 11) {
			return 1;
		} else {
			return 0;
		}
	}
}
