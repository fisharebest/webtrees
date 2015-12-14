<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageBas - Representation of the Basa (Cameroon) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBas extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'bas';
	}

	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
