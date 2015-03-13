<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTn - Representation of the Tswana language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'tn';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryBw;
	}
}
