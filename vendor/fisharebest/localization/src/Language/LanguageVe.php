<?php namespace Fisharebest\Localization;

/**
 * Class LanguageVe - Representation of the Venda language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 've';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}
}
