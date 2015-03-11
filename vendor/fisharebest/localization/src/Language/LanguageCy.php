<?php namespace Fisharebest\Localization;

/**
 * Class LanguageCy - Representation of the Welsh language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'cy';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGb;
	}
}
