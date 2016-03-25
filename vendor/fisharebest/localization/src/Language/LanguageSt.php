<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryLs;

/**
 * Class LanguageEn - Representation of the Sotho language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSt extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'st';
	}

	public function defaultTerritory() {
		return new TerritoryLs;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
