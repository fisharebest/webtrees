<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageMua - Representation of the Mundang language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMua extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'mua';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
