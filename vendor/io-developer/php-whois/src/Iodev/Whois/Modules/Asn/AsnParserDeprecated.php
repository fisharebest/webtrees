<?php

namespace Iodev\Whois\Modules\Asn;

trait AsnParserDeprecated
{
    /**
     * @deprecated will be removed in v4.2
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @deprecated will be removed in v4.2
     * @param string $className
     * @return self
     */
    public static function createByClass($className)
    {
        return new $className();
    }
}
