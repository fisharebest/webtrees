<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritorySn;

/**
 * Class LanguageFf - Representation of the Fulah language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFf extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ff';
	}

	public function defaultTerritory() {
		return new TerritorySn;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
