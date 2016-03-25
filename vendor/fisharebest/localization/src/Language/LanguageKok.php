<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptDeva;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageKok - Representation of the Konkani language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKok extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kok';
	}

	public function defaultScript() {
		return new ScriptDeva;
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
