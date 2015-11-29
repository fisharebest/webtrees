<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryMw;

/**
 * Class LanguageNy - Representation of the Chewa language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNy extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ny';
	}

	public function defaultTerritory() {
		return new TerritoryMw;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
