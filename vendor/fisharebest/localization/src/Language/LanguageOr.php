<?php namespace Fisharebest\Localization;

/**
 * Class LanguageOr - Representation of the Oriya language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageOr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'or';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptOrya;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
