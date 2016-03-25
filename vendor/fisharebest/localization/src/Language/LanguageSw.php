<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LanguageSw - Representation of the Swahili language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSw extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sw';
	}

	public function defaultTerritory() {
		return new TerritoryTz;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
