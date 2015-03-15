<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFf - Representation of the Fulah language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFf extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ff';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySn;
	}
}
