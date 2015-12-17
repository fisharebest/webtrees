<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule11 - Select a plural form for a specified number.
 *
 * Families:
 * Celtic (Irish Gaelic)
 *
 * nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : (n>2 && n<7) ? 2 :(n>6 && n<11) ? 3 : 4;
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule11 implements PluralRuleInterface {
	public function plurals() {
		return 5;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number === 1) {
			return 0;
		} elseif ($number === 2) {
			return 1;
		} elseif ($number > 2 && $number < 7) {
			return 2;
		} elseif ($number > 6 && $number < 11) {
			return 3;
		} else {
			return 4;
		}
	}
}
