<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageKs - Representation of the Kashmiri language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKs extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ks';
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}

	public function defaultScript() {
		return new ScriptArab;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
