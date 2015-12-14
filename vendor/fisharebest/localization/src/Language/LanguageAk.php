<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LanguageAk - Representation of the Akan language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAk extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ak';
	}

	public function defaultTerritory() {
		return new TerritoryGh;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
