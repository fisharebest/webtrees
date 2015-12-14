<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageKsf - Representation of the Bafia language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKsf extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ksf';
	}

	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
