<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryFr;

/**
 * Class LanguageBr - Representation of the Breton language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'br';
	}

	public function defaultTerritory() {
		return new TerritoryFr;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
