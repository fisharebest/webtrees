<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTibt - Representation of the Tibetan script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTibt extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Tibt';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('༠', '༡', '༢', '༣', '༤', '༥', '༦', '༧', '༨', '༩');
	}

	/** {@inheritdoc} */
	public function number() {
		return '330';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tibetan';
	}
}
