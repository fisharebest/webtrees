<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptBeng;
use Fisharebest\Localization\Territory\TerritoryBd;

/**
 * Class LanguageBn - Representation of the Bengali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'bn';
	}

	public function defaultScript() {
		return new ScriptBeng;
	}

	public function defaultTerritory() {
		return new TerritoryBd;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
