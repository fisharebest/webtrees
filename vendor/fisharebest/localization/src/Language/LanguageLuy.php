<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageLuy - Representation of the Luyia language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLuy extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'luy';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
