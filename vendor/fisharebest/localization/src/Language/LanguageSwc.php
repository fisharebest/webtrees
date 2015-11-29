<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LanguageSwc - Representation of the Congo Swahili language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSwc extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'swc';
	}

	public function defaultTerritory() {
		return new TerritoryCd;
	}
}
