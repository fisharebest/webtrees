<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIq;

/**
 * Class LanguageKu - Representation of the Kurdish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCkb extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ckb';
	}

	public function defaultScript() {
		return new ScriptArab;
	}

	public function defaultTerritory() {
		return new TerritoryIq;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
