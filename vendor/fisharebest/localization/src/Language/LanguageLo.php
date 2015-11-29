<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptLaoo;
use Fisharebest\Localization\Territory\TerritoryLa;

/**
 * Class LanguageLo - Representation of the Lao language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'lo';
	}

	public function defaultScript() {
		return new ScriptLaoo;
	}

	public function defaultTerritory() {
		return new TerritoryLa;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
