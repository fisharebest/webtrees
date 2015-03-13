<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTs - Representation of the Tsonga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ts';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}
}
