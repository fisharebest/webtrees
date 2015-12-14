<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageDua - Representation of the Duala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDua extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'dua';
	}

	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
