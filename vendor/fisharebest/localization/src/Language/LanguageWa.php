<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryBe;

/**
 * Class LanguageXh - Representation of the Walloon language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageWa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'wa';
	}

	public function defaultTerritory() {
		return new TerritoryBe;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
