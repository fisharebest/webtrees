<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFa - Representation of the Persian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fa';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIr;
	}
}
