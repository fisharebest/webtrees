<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIr;

/**
 * Class LanguageFa - Representation of the Persian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'fa';
	}

	public function defaultScript() {
		return new ScriptArab;
	}

	public function defaultTerritory() {
		return new TerritoryIr;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
