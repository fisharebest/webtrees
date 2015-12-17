<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LanguageTeo - Representation of the Teso language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTeo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'teo';
	}

	public function defaultTerritory() {
		return new TerritoryUg;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
