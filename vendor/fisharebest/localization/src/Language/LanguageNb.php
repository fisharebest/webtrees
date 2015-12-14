<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNo;

/**
 * Class LanguageNb - Representation of the Norwegian Bokmål language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNb extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'nb';
	}

	public function defaultTerritory() {
		return new TerritoryNo;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
