<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageEwo - Representation of the Ewondo language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEwo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ewo';
	}

	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
