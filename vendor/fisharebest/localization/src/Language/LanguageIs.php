<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule15;
use Fisharebest\Localization\Territory\TerritoryIs;

/**
 * Class LanguageIs - Representation of the Icelandic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIs extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'is';
	}

	public function defaultTerritory() {
		return new TerritoryIs;
	}

	public function pluralRule() {
		return new PluralRule15;
	}
}
