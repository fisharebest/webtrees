<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTr - Representation of the Turkish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'tr';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTr;
	}
}
