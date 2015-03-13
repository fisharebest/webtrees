<?php namespace Fisharebest\Localization;

/**
 * Class LanguageCa - Representation of the Catalan language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ca';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEs;
	}
}
