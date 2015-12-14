<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageHa - Representation of the Hausa language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ha';
	}

	public function defaultTerritory() {
		return new TerritoryNg;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
