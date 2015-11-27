<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageNmg - Representation of the Kwasio language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNmg extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'nmg';
	}

	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
