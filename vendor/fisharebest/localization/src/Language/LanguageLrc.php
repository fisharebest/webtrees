<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule12;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIr;

/**
 * Class LanguageLrc - Representation of the Luri language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLrc extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'lrc';
	}

	public function defaultScript() {
		return new ScriptArab;
	}

	public function defaultTerritory() {
		return new TerritoryIr;
	}

	public function pluralRule() {
		return new PluralRule12;
	}
}
