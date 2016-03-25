<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LanguageSma - Representation of the Southern Sami language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSma extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sma';
	}

	public function defaultTerritory() {
		return new TerritoryFi;
	}

	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
