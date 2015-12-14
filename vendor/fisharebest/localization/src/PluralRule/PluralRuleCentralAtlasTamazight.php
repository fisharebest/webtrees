<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRuleCentralAtlasTamazight - Select a plural form for a specified number.
 *
 * nplurals=4; plural=(n>=2 && n<=10 || n>99) ? 1 : 0
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRuleCentralAtlasTamazight implements PluralRuleInterface {
	public function plurals() {
		return 2;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number >= 2 && $number <= 10 || $number > 99) {
			return 1;
		} else {
			return 0;
		}
	}
}
