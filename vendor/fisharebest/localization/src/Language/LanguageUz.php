<?php namespace Fisharebest\Localization;

/**
 * Class LanguageUz - Representation of the Uzbek language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageUz extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'uz';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUz;
	}
}
