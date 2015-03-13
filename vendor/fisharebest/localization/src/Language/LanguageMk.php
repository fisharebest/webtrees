<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMk - Representation of the Macedonian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMk extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mk';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMk;
	}
}
