<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDsb - Representation of the Lower Sorbian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDsb extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dsb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDe;
	}
}
