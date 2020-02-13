<?php

namespace Iodev\Whois\Helpers;

trait GroupTrait
{
    /** @var array */
    private $groups = [];

    /** @var string */
    private $headerKey = '$header';

    /** @var array */
    private $domainKeys = [];

    /** @var array */
    private $subsetParams = [];

    /** @var bool */
    private $matchFirstOnly = false;

    /** @var bool */
    private $ignoreCase = false;

    /**
     * @return $this
     */
    public function cloneMe()
    {
        return clone $this;
    }

    /**
     * @return bool
     */
    public function isEmptyGroups()
    {
        return empty($this->groups);
    }

    /**
     * @return array
     */
    public function getFirstGroup()
    {
        return count($this->groups) ? $this->groups[0] : null;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     * @return $this
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * @param array $group
     * @return $this
     */
    public function setOneGroup($group)
    {
        $this->groups = $group ? [ $group ] : [];
        return $this;
    }

    /**
     * @return $this
     */
    public function useFirstGroup()
    {
        return $this->setOneGroup($this->getFirstGroup());
    }

    /**
     * @param array $group
     * @return $this
     */
    public function useFirstGroupOr($group)
    {
        $first = $this->getFirstGroup();
        return $this->setOneGroup(empty($first) ? $group : $first);
    }

    /**
     * @return $this
     */
    public function mergeGroups()
    {
        $finalGroup = [];
        foreach ($this->groups as $group) {
            $finalGroup = array_merge_recursive($finalGroup, $group);
        }
        $this->groups = [ $finalGroup ];
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setHeaderKey($key)
    {
        $this->headerKey = $key;
        return $this;
    }

    /**
     * @param array $keys
     * @return $this
     */
    public function setDomainKeys($keys)
    {
        $this->domainKeys = $keys;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setSubsetParams($params)
    {
        $this->subsetParams = $params;
        return $this;
    }

    /**
     * @param bool $val
     * @return $this
     */
    public function useMatchFirstOnly($val)
    {
        $this->matchFirstOnly = (bool)$val;
        return $this;
    }

    /**
     * @param bool $val
     * @return $this
     */
    public function useIgnoreCase($val)
    {
        $this->ignoreCase = (bool)$val;
        return $this;
    }
}
