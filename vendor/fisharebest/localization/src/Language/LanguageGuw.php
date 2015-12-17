<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryBj;

/**
 * Class LanguageGuw - Representation of the Gun language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGuw extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'guw';
	}

	public function defaultTerritory() {
		return new TerritoryBj;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
