<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMa;

/**
 * Class LanguageZgh - Representation of the Standard Moroccan Tamazight language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageZgh extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'zgh';
	}

	public function defaultTerritory() {
		return new TerritoryMa;
	}
}
