<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDav - Representation of the Taita language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDav extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dav';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
