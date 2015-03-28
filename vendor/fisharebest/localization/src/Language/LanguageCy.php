<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleWelsh;
use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LanguageCy - Representation of the Welsh language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCy extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'cy';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGb;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleWelsh;
	}
}
