<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZw;

/**
 * Class LanguageSn - Representation of the Shona language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sn';
	}

	public function defaultTerritory() {
		return new TerritoryZw;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
