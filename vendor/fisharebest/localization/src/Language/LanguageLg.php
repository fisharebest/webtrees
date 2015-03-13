<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLg - Representation of the Ganda language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUg;
	}
}
