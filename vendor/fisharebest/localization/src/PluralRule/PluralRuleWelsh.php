<?php namespace Fisharebest\Localization;

/**
 * Class PluralRuleWelsh - Select a plural form for a specified number.
 *
 * Families:
 * Celtic (Breton)
 *
 * nplurals=4; plural=(n==1) ? 0 : (n==2) ? 1 : (n != 8 && n != 11) ? 2 : 3;
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRuleWelsh implements PluralRuleInterface {
	/** {@inheritdoc} */
	public function plurals() {
		return 4;
	}

	/** {@inheritdoc} */
	public function plural($number) {
		$number = abs($number);

		if ($number === 1) {
			return 0;
		} elseif ($number === 2) {
			return 1;
		} elseif ($number !== 8 && $number !== 11) {
			return 2;
		} else {
			return 3;
		}
	}
}
