<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAst - Representation of the Asturian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAst extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ast';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEs;
	}
}
