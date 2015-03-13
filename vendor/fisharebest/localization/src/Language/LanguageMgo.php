<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMgo - Representation of the Meta' language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMgo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mgo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
