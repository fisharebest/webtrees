<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptKhmr;
use Fisharebest\Localization\Territory\TerritoryKh;

/**
 * Class LanguageKm - Representation of the Central Khmer language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKm extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'km';
	}

	public function defaultScript() {
		return new ScriptKhmr;
	}

	public function defaultTerritory() {
		return new TerritoryKh;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
