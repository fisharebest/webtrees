<?php namespace Fisharebest\Localization;

/**
 * Class LanguageVun - Representation of the Vunjo language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVun extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'vun';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
