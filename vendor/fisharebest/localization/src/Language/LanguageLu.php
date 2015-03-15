<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLu - Representation of the Luba-Katanga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lu';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCd;
	}
}
