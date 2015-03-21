<?php namespace Fisharebest\Localization;

/**
 * Class PluralRule2 - Select a plural form for a specified number.
 *
 * Families:
 * Romanic (French, Brazilian Portuguese)
 *
 * nplurals=2; plural=(n > 1);
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PluralRule2 implements PluralRuleInterface {
	/** {@inheritdoc} */
	public function plurals() {
		return 2;
	}

	/** {@inheritdoc} */
	public function plural($number) {
		$number = abs($number);

		if ($number > 1) {
			return 1;
		} else {
			return 0;
		}
	}
}
