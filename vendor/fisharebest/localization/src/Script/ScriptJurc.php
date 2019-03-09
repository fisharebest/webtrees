<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJurc - Representation of the Jurchen script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptJurc extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Jurc';
    }

    public function number()
    {
        return '510';
    }
}
