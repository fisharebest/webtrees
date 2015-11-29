<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEe;

/**
 * Class LanguageEt - Representation of the Estonian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEt extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'et';
	}

	public function defaultTerritory() {
		return new TerritoryEe;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
