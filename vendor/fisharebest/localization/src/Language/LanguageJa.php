<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptJpan;
use Fisharebest\Localization\Territory\TerritoryJp;

/**
 * Class LanguageJa - Representation of the Japanese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageJa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ja';
	}

	public function defaultScript() {
		return new ScriptJpan;
	}

	public function defaultTerritory() {
		return new TerritoryJp;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
