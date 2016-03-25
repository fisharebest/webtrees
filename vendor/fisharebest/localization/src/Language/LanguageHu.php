<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryHu;

/**
 * Class LanguageHu - Representation of the Hungarian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'hu';
	}

	public function defaultTerritory() {
		return new TerritoryHu;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
