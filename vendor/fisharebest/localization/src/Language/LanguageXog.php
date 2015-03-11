<?php namespace Fisharebest\Localization;

/**
 * Class LanguageXog - Representation of the Soga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageXog extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'xog';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUg;
	}
}
