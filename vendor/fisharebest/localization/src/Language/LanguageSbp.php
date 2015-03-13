<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSbp - Representation of the Sangu (Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSbp extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sbp';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
