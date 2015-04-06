<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptVaii;
use Fisharebest\Localization\Territory\TerritoryLr;

/**
 * Class LanguageVai - Representation of the Vai language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVai extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'vai';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptVaii;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLr;
	}
}
