<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryMl;

/**
 * Class LanguageBm - Representation of the Bambara language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBm extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'bm';
	}

	public function defaultTerritory() {
		return new TerritoryMl;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
