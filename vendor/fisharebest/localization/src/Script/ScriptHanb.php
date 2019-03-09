<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHanb - Representation of the Hangul+Bopomofo script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptHanb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hanb';
    }

    public function number()
    {
        return '503';
    }
}
