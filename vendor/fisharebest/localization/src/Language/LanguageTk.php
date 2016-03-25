<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTm;

/**
 * Class LanguageTk - Representation of the Turkmen language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTk extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'tk';
	}

	public function defaultTerritory() {
		return new TerritoryTm;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
