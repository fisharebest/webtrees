<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUz;

/**
 * Class LanguageUz - Representation of the Uzbek language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageUz extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'uz';
	}

	public function defaultTerritory() {
		return new TerritoryUz;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
