<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptHebr;
use Fisharebest\Localization\Territory\TerritoryIl;

/**
 * Class LanguageHe - Representation of the Hebrew language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHe extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'he';
	}

	public function defaultScript() {
		return new ScriptHebr;
	}

	public function defaultTerritory() {
		return new TerritoryIl;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
