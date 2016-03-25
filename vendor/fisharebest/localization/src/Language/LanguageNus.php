<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritorySd;

/**
 * Class LanguageNus - Representation of the Nuer language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNus extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'nus';
	}

	public function defaultTerritory() {
		return new TerritorySd;
	}
}
