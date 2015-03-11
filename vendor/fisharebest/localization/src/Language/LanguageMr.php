<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMr - Representation of the Marathi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mr';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptDeva;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
