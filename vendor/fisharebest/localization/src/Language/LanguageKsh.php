<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKsh - Representation of the Kölsch language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKsh extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ksh';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDe;
	}
}
