<?php namespace Fisharebest\Localization;

/**
 * Class LanguageShi - Representation of the Tachelhit language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageShi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'shi';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptTfng;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMa;
	}
}
