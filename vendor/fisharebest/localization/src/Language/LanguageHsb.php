<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule10;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageHsb - Representation of the Upper Sorbian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHsb extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'hsb';
	}

	public function defaultTerritory() {
		return new TerritoryDe;
	}

	public function pluralRule() {
		return new PluralRule10;
	}
}
