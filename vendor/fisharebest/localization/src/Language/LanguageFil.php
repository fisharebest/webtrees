<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFil - Representation of the Filipino language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFil extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fil';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPh;
	}
}
