<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryTr;

/**
 * Class LanguageTr - Representation of the Turkish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'tr';
	}

	public function defaultTerritory() {
		return new TerritoryTr;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
