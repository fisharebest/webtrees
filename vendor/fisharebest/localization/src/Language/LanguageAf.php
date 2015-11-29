<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageAf - Representation of the Afrikaans language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAf extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'af';
	}

	public function defaultTerritory() {
		return new TerritoryZa;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
