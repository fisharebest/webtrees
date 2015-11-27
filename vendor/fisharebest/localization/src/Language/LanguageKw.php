<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleCornish;
use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LanguageKw - Representation of the Cornish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKw extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kw';
	}

	public function defaultTerritory() {
		return new TerritoryGb;
	}

	public function pluralRule() {
		return new PluralRuleCornish;
	}
}
