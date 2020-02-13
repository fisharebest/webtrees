<?php

namespace Iodev\Whois\Modules\Tld\Parsers;

use Iodev\Whois\Helpers\DateHelper;
use Iodev\Whois\Helpers\GroupFilter;
use Iodev\Whois\Modules\Tld\DomainInfo;
use Iodev\Whois\Modules\Tld\DomainResponse;
use Iodev\Whois\Modules\Tld\TldParser;

class BlockParser extends CommonParser
{
    /** @var array */
    protected $reservedDomainKeys = [ "Reserved name" ];

    /** @var array */
    protected $reservedDomainSubsets = [];

    /** @var array */
    protected $domainSubsets = [];

    /** @var array */
    protected $primarySubsets = [];

    /** @var array */
    protected $statesSubsets = [];

    /** @var array */
    protected $nameServersSubsets = [];

    /** @var array */
    protected $nameServersSparsedSubsets = [];

    /** @var array */
    protected $ownerSubsets = [];

    /** @var array */
    protected $registrarSubsets = [];

    /** @var array */
    protected $registrarReservedSubsets = [];

    /** @var array */
    protected $registrarReservedKeys = [];

    /** @var array */
    protected $contactSubsets = [];

    /** @var array */
    protected $contactOrgKeys = [];

    /** @var array */
    protected $registrarGroupKeys = [];


    /** @var string */
    protected $matchedDomain = '';

    /**
     * @return string
     */
    public function getType()
    {
        return TldParser::BLOCK;
    }

    /**
     * @param DomainResponse $response
     * @return DomainInfo
     */
    public function parseResponse(DomainResponse $response)
    {
        $groups = $this->groupsFromText($response->getText());
        $rootFilter = GroupFilter::create($groups)
            ->useIgnoreCase(true)
            ->handleEmpty($this->emptyValuesDict)
            ->setHeaderKey($this->headerKey)
            ->setDomainKeys($this->domainKeys)
            ->setSubsetParams([ '$domain' => $response->getDomain() ]);

        $reserved = $rootFilter->cloneMe()
            ->filterHasSubsetOf($this->reservedDomainSubsets)
            ->toSelector()
            ->selectKeys($this->reservedDomainKeys)
            ->getFirst();

        $isReserved = !empty($reserved);

        $domainFilter = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetOf($this->domainSubsets);

        $primaryFilter = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetOf($this->primarySubsets)
            ->useFirstGroupOr($domainFilter->getFirstGroup());

        $info = new DomainInfo($response, [
            "domainName" => $this->parseDomain($domainFilter) ?: ($isReserved ? $response->getDomain() : ''),
            "states" => $this->parseStates($rootFilter, $primaryFilter),
            "nameServers" => $this->parseNameServers($rootFilter, $primaryFilter),
            "owner" => $this->parseOwner($rootFilter, $primaryFilter) ?: ($isReserved ? $reserved : ''),
            "registrar" => $this->parseRegistrar($rootFilter, $primaryFilter),
            "creationDate" => $this->parseCreationDate($rootFilter, $primaryFilter),
            "expirationDate" => $this->parseExpirationDate($rootFilter, $primaryFilter),
            "whoisServer" => $this->parseWhoisServer($rootFilter, $primaryFilter),
        ], $this->getType());
        return $isReserved || $info->isValuable($this->notRegisteredStatesDict) ? $info : null;
    }

    /**
     * @param GroupFilter $domainFilter
     * @return string
     */
    protected function parseDomain(GroupFilter $domainFilter)
    {
        $sel = $domainFilter
            ->toSelector()
            ->selectKeys($this->domainKeys)
            ->removeEmpty();
        $this->matchedDomain = $sel->getFirst('');

        $domain = $sel->mapDomain()->removeEmpty()->getFirst('');
        if (!empty($domain)) {
            return $domain;
        }

        $sel = $domainFilter->cloneMe()
            ->filterHasHeader()
            ->toSelector()
            ->selectKeys([ 'name' ])
            ->removeEmpty();
        $this->matchedDomain = $sel->getFirst('');

        return $sel->mapDomain()->removeEmpty()->getFirst('');
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return array
     */
    protected function parseStates(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        $states = $primaryFilter->toSelector()
            ->selectKeys($this->statesKeys)
            ->mapStates()
            ->removeEmpty()
            ->removeDuplicates()
            ->getAll();

        if (!empty($states)) {
            return $states;
        }

        $extraStates = [];
        if ($this->matchedDomain && preg_match('~is\s+(.+)$~', $this->matchedDomain, $m)) {
            $extraStates = [ $m[1] ];
        }
        return $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetOf($this->statesSubsets)
            ->toSelector()
            ->selectItems($extraStates)
            ->selectKeys($this->statesKeys)
            ->mapStates()
            ->removeEmpty()
            ->removeDuplicates()
            ->getAll();
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return array
     */
    protected function parseNameServers(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        $nameServers = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetOf($this->nameServersSubsets)
            ->useFirstGroup()
            ->toSelector()
            ->selectKeys($this->nameServersKeys)
            ->selectKeyGroups($this->nameServersKeysGroups)
            ->mapAsciiServer()
            ->removeEmpty()
            ->getAll();

        $nameServers = $rootFilter->cloneMe()
            ->filterHasSubsetOf($this->nameServersSparsedSubsets)
            ->toSelector()
            ->useMatchFirstOnly(true)
            ->selectItems($nameServers)
            ->selectKeys($this->nameServersKeys)
            ->selectKeyGroups($this->nameServersKeysGroups)
            ->mapAsciiServer()
            ->removeEmpty()
            ->removeDuplicates()
            ->getAll();

        if (!empty($nameServers)) {
            return $nameServers;
        }
        return $primaryFilter->toSelector()
            ->useMatchFirstOnly(true)
            ->selectKeys($this->nameServersKeys)
            ->selectKeyGroups($this->nameServersKeysGroups)
            ->mapAsciiServer()
            ->removeEmpty()
            ->removeDuplicates()
            ->getAll();
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return string
     */
    protected function parseOwner(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        $owner = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetOf($this->ownerSubsets)
            ->toSelector()
            ->selectKeys($this->ownerKeys)
            ->getFirst('');

        if (empty($owner)) {
            $owner = $primaryFilter->toSelector()
                ->selectKeys($this->ownerKeys)
                ->getFirst('');
        }
        if (!empty($owner)) {
            $owner = $rootFilter->cloneMe()
                ->setSubsetParams(['$id' => $owner])
                ->useMatchFirstOnly(true)
                ->filterHasSubsetOf($this->contactSubsets)
                ->toSelector()
                ->selectKeys($this->contactOrgKeys)
                ->selectItems([ $owner ])
                ->removeEmpty()
                ->getFirst('');
        }
        return $owner;
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return string
     */
    protected function parseRegistrar(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        $registrar = $primaryFilter->toSelector()
            ->useMatchFirstOnly(true)
            ->selectKeys($this->registrarKeys)
            ->getFirst();

        if (empty($registrar)) {
            $registrarFilter = $rootFilter->cloneMe()
                ->useMatchFirstOnly(true)
                ->filterHasSubsetOf($this->registrarSubsets);

            $registrar = $registrarFilter->toSelector()
                ->selectKeys($this->registrarGroupKeys)
                ->getFirst();
        }
        if (empty($registrar) && !empty($registrarFilter)) {
            $registrar = $registrarFilter->filterHasHeader()
                ->toSelector()
                ->selectKeys([ 'name' ])
                ->getFirst();
        }
        if (empty($registrar)) {
            $registrar = $primaryFilter->toSelector()
                ->selectKeys($this->registrarKeys)
                ->getFirst();
        }

        $regFilter = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetOf($this->registrarReservedSubsets);

        $regId = $regFilter->toSelector()
            ->selectKeys($this->registrarReservedKeys)
            ->getFirst();

        if (!empty($regId) && (empty($registrar) || $regFilter->getFirstGroup() != $primaryFilter->getFirstGroup())) {
            $registrarOrg = $rootFilter->cloneMe()
                ->setSubsetParams(['$id' => $regId])
                ->useMatchFirstOnly(true)
                ->filterHasSubsetOf($this->contactSubsets)
                ->toSelector()
                ->selectKeys($this->contactOrgKeys)
                ->getFirst();

            $owner = $this->parseOwner($rootFilter, $primaryFilter);
            $registrar = ($registrarOrg && $registrarOrg != $owner)
                ? $registrarOrg
                : $registrar;
        }

        return $registrar;
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return int
     */
    protected function parseCreationDate(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        $time = $primaryFilter->toSelector()
            ->selectKeys($this->creationDateKeys)
            ->mapUnixTime($this->getOption('inversedDateMMDD', false))
            ->getFirst(0);

        if (!empty($time)) {
            return $time;
        }

        $sel = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetKeyOf($this->creationDateKeys)
            ->toSelector()
            ->selectKeys($this->creationDateKeys);

        $time = $sel->cloneMe()
            ->mapUnixTime($this->getOption('inversedDateMMDD', false))
            ->getFirst(0);

        if (!empty($time)) {
            return $time;
        }

        foreach ($sel->getAll() as $str) {
            if (preg_match('~registered\s+on\b~ui', $str)) {
                $time = DateHelper::parseDateInText($str);
                if (!empty($time)) {
                    return $time;
                }
            }
        }
        return 0;
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return int
     */
    protected function parseExpirationDate(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        $time = $primaryFilter->toSelector()
            ->selectKeys($this->expirationDateKeys)
            ->mapUnixTime($this->getOption('inversedDateMMDD', false))
            ->getFirst();

        if (!empty($time)) {
            return $time;
        }

        $sel = $rootFilter->cloneMe()
            ->useMatchFirstOnly(true)
            ->filterHasSubsetKeyOf($this->expirationDateKeys)
            ->toSelector()
            ->selectKeys($this->expirationDateKeys);

        $time = $sel->cloneMe()
            ->mapUnixTime($this->getOption('inversedDateMMDD', false))
            ->getFirst(0);

        if (!empty($time)) {
            return $time;
        }

        foreach ($sel->getAll() as $str) {
            if (preg_match('~registry\s+fee\s+due\s+on\b~ui', $str)) {
                $time = DateHelper::parseDateInText($str);
                if (!empty($time)) {
                    return $time;
                }
            }
        }
        return 0;
    }

    /**
     * @param GroupFilter $rootFilter
     * @param GroupFilter $primaryFilter
     * @return mixed
     */
    protected function parseWhoisServer(GroupFilter $rootFilter, GroupFilter $primaryFilter)
    {
        return $primaryFilter->toSelector()
            ->selectKeys($this->whoisServerKeys)
            ->mapAsciiServer()
            ->getFirst('');
    }
}
