<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEs;

/**
 * Class LanguageGl - Representation of the Galician language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGl extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'gl';
	}

	public function defaultTerritory() {
		return new TerritoryEs;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
