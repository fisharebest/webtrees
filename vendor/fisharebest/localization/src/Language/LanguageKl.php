<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryGl;

/**
 * Class LanguageKl - Representation of the Kalaallisut language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKl extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kl';
	}

	public function defaultTerritory() {
		return new TerritoryGl;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
