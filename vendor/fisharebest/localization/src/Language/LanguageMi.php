<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryNz;

/**
 * Class LanguageEn - Representation of the Maori language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMi extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'mi';
	}

	public function defaultTerritory() {
		return new TerritoryNz;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
