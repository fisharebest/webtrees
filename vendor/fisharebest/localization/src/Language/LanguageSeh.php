<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryMz;

/**
 * Class LanguageSeh - Representation of the Sena language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSeh extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'seh';
	}

	public function defaultTerritory() {
		return new TerritoryMz;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
