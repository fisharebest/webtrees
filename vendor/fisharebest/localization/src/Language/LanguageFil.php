<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryPh;

/**
 * Class LanguageFil - Representation of the Filipino language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFil extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'fil';
	}

	public function defaultTerritory() {
		return new TerritoryPh;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
