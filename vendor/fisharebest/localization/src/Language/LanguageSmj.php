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
	/** {@inheritdoc} */
	public function code() {
		return 'smj';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySe;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
