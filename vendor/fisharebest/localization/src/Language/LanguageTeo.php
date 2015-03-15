<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTeo - Representation of the Teso language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTeo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'teo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUg;
	}
}
