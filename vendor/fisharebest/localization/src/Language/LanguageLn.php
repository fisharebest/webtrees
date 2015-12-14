<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LanguageLn - Representation of the Lingala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ln';
	}

	public function defaultTerritory() {
		return new TerritoryCd;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
