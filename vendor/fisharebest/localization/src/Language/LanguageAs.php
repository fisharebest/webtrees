<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptBeng;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageAs - Representation of the Assamese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAs extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'as';
	}

	public function defaultScript() {
		return new ScriptBeng;
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
