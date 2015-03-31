<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LanguageSbp - Representation of the Sangu (Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSbp extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'sbp';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
