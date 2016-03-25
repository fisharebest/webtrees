<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryRu;

/**
 * Class LanguageOs - Representation of the Ossetian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageOs extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'os';
	}

	public function defaultTerritory() {
		return new TerritoryRu;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
