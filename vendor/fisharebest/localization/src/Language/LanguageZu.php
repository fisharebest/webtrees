<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageZu - Representation of the Zulu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageZu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'zu';
	}

	public function defaultTerritory() {
		return new TerritoryZa;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
