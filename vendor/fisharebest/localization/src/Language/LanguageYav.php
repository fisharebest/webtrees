<?php namespace Fisharebest\Localization;

/**
 * Class LanguageYav - Representation of the Yangben language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageYav extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'yav';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
