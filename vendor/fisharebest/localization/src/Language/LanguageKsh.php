<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleZeroOneOther;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageKsh - Representation of the Kölsch language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKsh extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ksh';
	}

	public function defaultTerritory() {
		return new TerritoryDe;
	}

	public function pluralRule() {
		return new PluralRuleZeroOneOther;
	}
}
