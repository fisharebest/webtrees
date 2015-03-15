<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMer - Representation of the Meru language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMer extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mer';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
