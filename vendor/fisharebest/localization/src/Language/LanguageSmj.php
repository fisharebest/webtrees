<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LanguageSms - Representation of the Lule Sami language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSmj extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'smj';
	}

	public function defaultTerritory() {
		return new TerritorySe;
	}

	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
