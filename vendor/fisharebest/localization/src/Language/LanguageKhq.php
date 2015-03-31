<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMl;

/**
 * Class LanguageKhq - Representation of the Koyra Chiini Songhay language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKhq extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'khq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMl;
	}
}
