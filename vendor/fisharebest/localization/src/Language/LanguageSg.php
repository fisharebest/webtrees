<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSg - Representation of the Sango language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCf;
	}
}
