<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptEthi;
use Fisharebest\Localization\Territory\TerritoryEt;

/**
 * Class LanguageAm - Representation of the Amharic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAm extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'am';
	}

	public function defaultScript() {
		return new ScriptEthi;
	}

	public function defaultTerritory() {
		return new TerritoryEt;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
