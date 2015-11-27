<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryTo;

/**
 * Class LanguageTo - Representation of the Tonga (Tonga Islands) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'to';
	}

	public function defaultTerritory() {
		return new TerritoryTo;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
