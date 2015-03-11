<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKok - Representation of the Konkani language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKok extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kok';
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
