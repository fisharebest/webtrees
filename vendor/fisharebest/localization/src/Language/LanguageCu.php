<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryRu;

/**
 * Class LanguageCu - Representation of the Old Church Slavonic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'cu';
	}

	public function defaultScript() {
		return new ScriptCyrl;
	}

	public function defaultTerritory() {
		return new TerritoryRu;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
