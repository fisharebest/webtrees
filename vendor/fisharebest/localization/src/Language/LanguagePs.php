<?php namespace Fisharebest\Localization;

/**
 * Class LanguagePs - Representation of the Pushto language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ps';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPk;
	}
}
