<?php namespace Fisharebest\Localization;

/**
 * Class LocalePtBr - Brazilian Portuguese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtBr extends LocalePt {
	/** {@inheritdoc} */
	public function endonym() {
		return 'português do Brasil';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'PORTUGUES DO BRASIL';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBr;
	}
}
