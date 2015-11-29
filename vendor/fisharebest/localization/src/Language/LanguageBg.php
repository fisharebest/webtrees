<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryBg;

/**
 * Class LanguageBg - Representation of the Bulgarian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBg extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'bg';
	}

	public function defaultScript() {
		return new ScriptCyrl;
	}

	public function defaultTerritory() {
		return new TerritoryBg;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
