<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryRu;

/**
 * Class LanguageTt - Representation of the Tatar language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTt extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'tt';
	}

	public function defaultScript() {
		return new ScriptCyrl;
	}

	public function defaultTerritory() {
		return new TerritoryRu;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
