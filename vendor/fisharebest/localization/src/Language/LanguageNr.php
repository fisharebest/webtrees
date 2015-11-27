<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageNr - Representation of the South Ndebele language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'nr';
	}

	public function defaultTerritory() {
		return new TerritoryZa;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
