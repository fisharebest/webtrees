<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptGeor;
use Fisharebest\Localization\Territory\TerritoryGe;

/**
 * Class LanguageKa - Representation of the Georgian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ka';
	}

	public function defaultScript() {
		return new ScriptGeor;
	}

	public function defaultTerritory() {
		return new TerritoryGe;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
