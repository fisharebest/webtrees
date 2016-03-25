<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptTelu;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageTe - Representation of the Telugu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTe extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'te';
	}

	public function defaultScript() {
		return new ScriptTelu;
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
