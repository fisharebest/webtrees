<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNl;

/**
 * Class LanguageNl - Representation of the Dutch language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNl extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'nl';
	}

	public function defaultTerritory() {
		return new TerritoryNl;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
