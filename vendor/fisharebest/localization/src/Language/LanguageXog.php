<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LanguageXog - Representation of the Soga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageXog extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'xog';
	}

	public function defaultTerritory() {
		return new TerritoryUg;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
