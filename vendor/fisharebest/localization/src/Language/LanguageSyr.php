<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptSyrc;
use Fisharebest\Localization\Territory\TerritoryIq;

/**
 * Class LanguageSyr - Representation of the Syriac language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSyr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'syr';
	}

	public function defaultScript() {
		return new ScriptSyrc;
	}

	public function defaultTerritory() {
		return new TerritoryIq;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
