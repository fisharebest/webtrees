<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNr - Representation of the South Ndebele language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nr';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}
}
