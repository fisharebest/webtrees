<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LanguageGsw - Representation of the Swiss German language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGsw extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'gsw';
	}

	public function defaultTerritory() {
		return new TerritoryCh;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
