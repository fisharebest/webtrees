<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptSinh;
use Fisharebest\Localization\Territory\TerritoryLk;

/**
 * Class LanguageSi - Representation of the Sinhala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSi extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'si';
	}

	public function defaultScript() {
		return new ScriptSinh;
	}

	public function defaultTerritory() {
		return new TerritoryLk;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
