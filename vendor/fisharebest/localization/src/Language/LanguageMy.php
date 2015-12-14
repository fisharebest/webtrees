<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptMymr;
use Fisharebest\Localization\Territory\TerritoryMm;

/**
 * Class LanguageMy - Representation of the Burmese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMy extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'my';
	}

	public function defaultScript() {
		return new ScriptMymr;
	}

	public function defaultTerritory() {
		return new TerritoryMm;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
