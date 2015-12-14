<?php namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule7 - Select a plural form for a specified number.
 *
 * Families:
 * Slavic (Belarusian, Bosnian, Croatian, Serbian, Russian, Ukrainian)
 *
 * nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule7 implements PluralRuleInterface {
	public function plurals() {
		return 3;
	}

	public function plural($number) {
		$number = abs($number);

		if ($number % 10 === 1 && $number % 100 !== 11) {
			return 0;
		} elseif ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
			return 1;
		} else {
			return 2;
		}
	}
}
