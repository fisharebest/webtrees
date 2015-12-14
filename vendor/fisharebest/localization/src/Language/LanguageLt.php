<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule6;
use Fisharebest\Localization\Territory\TerritoryLt;

/**
 * Class LanguageLt - Representation of the Lithuanian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLt extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'lt';
	}

	public function defaultTerritory() {
		return new TerritoryLt;
	}

	public function pluralRule() {
		return new PluralRule6;
	}
}
