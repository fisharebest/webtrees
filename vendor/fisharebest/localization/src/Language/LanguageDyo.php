<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDyo - Representation of the Jola-Fonyi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDyo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dyo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySn;
	}
}
