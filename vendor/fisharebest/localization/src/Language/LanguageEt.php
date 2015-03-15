<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEt - Representation of the Estonian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'et';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEe;
	}
}
