<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTr;

/**
 * Class LanguageKu - Representation of the Kurdish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ku';
	}

	public function defaultTerritory() {
		return new TerritoryTr;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
