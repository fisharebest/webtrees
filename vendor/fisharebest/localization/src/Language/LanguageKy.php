<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryKg;

/**
 * Class LanguageKy - Representation of the Kirghiz language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKy extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ky';
	}

	public function defaultTerritory() {
		return new TerritoryKg;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
