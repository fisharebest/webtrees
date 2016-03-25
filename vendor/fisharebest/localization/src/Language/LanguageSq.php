<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryAl;

/**
 * Class LanguageSq - Representation of the Albanian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSq extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sq';
	}

	public function defaultTerritory() {
		return new TerritoryAl;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
