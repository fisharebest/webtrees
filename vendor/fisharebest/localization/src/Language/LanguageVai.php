<?php namespace Fisharebest\Localization;

/**
 * Class LanguageVai - Representation of the Vai language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVai extends Language {
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
