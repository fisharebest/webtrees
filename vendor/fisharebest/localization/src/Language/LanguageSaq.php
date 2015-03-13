<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSaq - Representation of the Samburu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSaq extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'saq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
