<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule11;
use Fisharebest\Localization\Territory\TerritoryIe;

/**
 * Class LanguageGa - Representation of the Irish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ga';
	}

	public function defaultTerritory() {
		return new TerritoryIe;
	}

	public function pluralRule() {
		return new PluralRule11;
	}
}
