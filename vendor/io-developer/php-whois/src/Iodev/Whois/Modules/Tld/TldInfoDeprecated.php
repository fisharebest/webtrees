<?php

namespace Iodev\Whois\Modules\Tld;

trait TldInfoDeprecated
{
    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getParserType()
    {
        return $this->parserType;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getWhoisServer()
    {
        return $this->whoisServer;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string[]
     */
    public function getNameServers()
    {
        return $this->nameServers;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return int
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string[]
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getRegistrar()
    {
        return $this->registrar;
    }
}
