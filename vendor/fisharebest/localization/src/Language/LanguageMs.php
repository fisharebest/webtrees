<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMs - Representation of the Malay language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ms';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMy;
	}
}
