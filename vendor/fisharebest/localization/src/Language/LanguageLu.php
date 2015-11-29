<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LanguageLu - Representation of the Luba-Katanga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'lu';
	}

	public function defaultTerritory() {
		return new TerritoryCd;
	}
}
