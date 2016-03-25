<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryFr;

/**
 * Class LanguageFr - Representation of the French language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'fr';
	}

	public function defaultTerritory() {
		return new TerritoryFr;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
