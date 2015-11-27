<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryMu;

/**
 * Class LanguageMfe - Representation of the Morisyen language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMfe extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'mfe';
	}

	public function defaultTerritory() {
		return new TerritoryMu;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
