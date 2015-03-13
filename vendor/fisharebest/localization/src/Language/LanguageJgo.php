<?php namespace Fisharebest\Localization;

/**
 * Class LanguageJgo - Representation of the Ngomba language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageJgo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'jgo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
