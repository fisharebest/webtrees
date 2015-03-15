<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLuy - Representation of the Luyia language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLuy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'luy';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
