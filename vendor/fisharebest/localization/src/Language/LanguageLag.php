<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleZeroOneOther;
use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LanguageLag - Representation of the Langi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLag extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'lag';
	}

	public function defaultTerritory() {
		return new TerritoryTz;
	}

	public function pluralRule() {
		return new PluralRuleZeroOneOther;
	}
}
