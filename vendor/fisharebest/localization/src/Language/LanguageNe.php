<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptDeva;
use Fisharebest\Localization\Territory\TerritoryNp;

/**
 * Class LanguageNe - Representation of the Nepali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNe extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ne';
	}

	public function defaultScript() {
		return new ScriptDeva;
	}

	public function defaultTerritory() {
		return new TerritoryNp;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
