<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKa - Representation of the Georgian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ka';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptGeor;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGe;
	}
}
