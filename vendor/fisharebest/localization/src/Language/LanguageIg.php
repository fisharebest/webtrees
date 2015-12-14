<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageIg - Representation of the Igbo language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIg extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ig';
	}

	public function defaultTerritory() {
		return new TerritoryNg;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
