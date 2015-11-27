<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMl;

/**
 * Class LanguageKhq - Representation of the Koyra Chiini Songhay language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKhq extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'khq';
	}

	public function defaultTerritory() {
		return new TerritoryMl;
	}
}
