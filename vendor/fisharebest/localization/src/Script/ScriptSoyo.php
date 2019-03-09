<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Soyombo script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptSoyo extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Soyo';
    }

    public function number()
    {
        return '329';
    }

    public function unicodeName()
    {
        return 'Soyombo';
    }
}
