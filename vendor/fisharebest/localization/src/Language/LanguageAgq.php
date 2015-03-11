<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAgq - Representation of the Aghem language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAgq extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'agq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
