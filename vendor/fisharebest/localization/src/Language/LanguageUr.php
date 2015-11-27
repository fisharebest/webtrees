<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryPk;

/**
 * Class LanguageUr - Representation of the Urdu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageUr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ur';
	}

	public function defaultScript() {
		return new ScriptArab;
	}

	public function defaultTerritory() {
		return new TerritoryPk;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
