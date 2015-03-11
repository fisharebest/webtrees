<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDje - Representation of the Zarma language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDje extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dje';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNe;
	}
}
