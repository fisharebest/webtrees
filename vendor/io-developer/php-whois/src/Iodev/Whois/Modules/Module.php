<?php

namespace Iodev\Whois\Modules;

use Iodev\Whois\Loaders\ILoader;

abstract class Module
{
    /**
     * @param string $type
     * @param ILoader $loader
     */
    public function __construct($type, ILoader $loader)
    {
        $this->type = strval($type);
        $this->loader = $loader;
    }

    /** @var string */
    private $type;

    /** @var ILoader */
    private $loader;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return ILoader
     */
    public function getLoader()
    {
        return $this->loader;
    }
}
