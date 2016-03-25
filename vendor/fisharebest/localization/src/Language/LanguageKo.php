<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptKore;
use Fisharebest\Localization\Territory\TerritoryKr;

/**
 * Class LanguageKo - Representation of the Korean language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ko';
	}

	public function defaultScript() {
		return new ScriptKore;
	}

	public function defaultTerritory() {
		return new TerritoryKr;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
