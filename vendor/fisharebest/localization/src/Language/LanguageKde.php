<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LanguageKde - Representation of the Makonde language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKde extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kde';
	}

	public function defaultTerritory() {
		return new TerritoryTz;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
