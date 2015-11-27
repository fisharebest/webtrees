<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptKnda;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageKn - Representation of the Kannada language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kn';
	}

	public function defaultScript() {
		return new ScriptKnda;
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}

	public function pluralRule() {
		return new PluralRule2;
	}
}
