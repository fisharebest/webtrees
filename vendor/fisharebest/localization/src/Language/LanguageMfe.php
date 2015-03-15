<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMfe - Representation of the Morisyen language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMfe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mfe';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMu;
	}
}
