<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEr;

/**
 * Class LanguageSsy - Representation of the Saho language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSsy extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ssy';
	}

	public function defaultTerritory() {
		return new TerritoryEr;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
