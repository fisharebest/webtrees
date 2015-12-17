<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LanguageSmn - Representation of the Inari Sami language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSmn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'smn';
	}

	public function defaultTerritory() {
		return new TerritoryFi;
	}

	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
