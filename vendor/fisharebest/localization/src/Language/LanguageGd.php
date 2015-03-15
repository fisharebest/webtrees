<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGd - Representation of the Scottish Gaelic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGd extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'gd';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGb;
	}
}
