<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSeh - Representation of the Sena language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSeh extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'seh';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMz;
	}
}
