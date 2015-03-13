<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKs - Representation of the Kashmiri language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ks';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}
}
