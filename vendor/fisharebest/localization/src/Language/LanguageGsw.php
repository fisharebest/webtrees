<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGsw - Representation of the Swiss German language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGsw extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'gsw';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCh;
	}
}
