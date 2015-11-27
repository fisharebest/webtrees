<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptGuru;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguagePa - Representation of the Panjabi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'pa';
	}

	public function defaultScript() {
		return new ScriptGuru;
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
