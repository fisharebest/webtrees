<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LanguageSms - Representation of the Skolt Sami language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSms extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'sms';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryFi;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
