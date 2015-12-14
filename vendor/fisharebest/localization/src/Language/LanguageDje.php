<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryNe;

/**
 * Class LanguageDje - Representation of the Zarma language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDje extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'dje';
	}

	public function defaultTerritory() {
		return new TerritoryNe;
	}
}
