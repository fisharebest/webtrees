<?php

namespace Iodev\Whois\Helpers;

class GroupSelector
{
    use GroupTrait;

    /**
     * @param array $groups
     * @return $this
     */
    public static function create($groups = [])
    {
        $m = new self();
        return $m->setGroups($groups);
    }

    /** @var array */
    private $items = [];

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getFirst($default = null)
    {
        return empty($this->items) ? $default : reset($this->items);
    }

    /**
     * @return $this
     */
    public function clean()
    {
        $this->items = [];
        return $this;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function selectItems($items)
    {
        $this->items = array_merge($this->items, $items);
        return $this;
    }

    /**
     * @param string[] $keys
     * @return $this
     */
    public function selectKeys($keys)
    {
        foreach ($this->groups as $group) {
            $matches = GroupHelper::match($group, $keys, $this->ignoreCase, $this->matchFirstOnly);
            foreach ($matches as $match) {
                if (is_array($match)) {
                    $this->items = array_merge($this->items, $match);
                } else {
                    $this->items[] = $match;
                }
            }
        }
        return $this;
    }

    /**
     * @param array $keyGroups
     * @return $this
     */
    public function selectKeyGroups($keyGroups)
    {
        foreach ($keyGroups as $keyGroup) {
            foreach ($keyGroup as $key) {
                $this->selectKeys([ $key ]);
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function removeEmpty()
    {
        $this->items = array_filter($this->items);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeDuplicates()
    {
        $this->items = array_unique($this->items);
        return $this;
    }

    /**
     * @return $this
     */
    public function mapDomain()
    {
        foreach ($this->items as &$item) {
            if ($item && preg_match('~([-\pL\d]+\.)+[-\pL\d]+~ui', $item, $m)) {
                $item = DomainHelper::filterAscii(DomainHelper::toAscii($m[0]));
            } else {
                $item = '';
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function mapAsciiServer()
    {
        foreach ($this->items as &$item) {
            $item = DomainHelper::filterAscii(DomainHelper::toAscii(is_string($item) ? $item : ''));
            if ($item && !preg_match('~^([-\pL\d]+\.)+[-\pL\d]+$~ui', $item)) {
                if (!preg_match('~^[a-z\d]+-norid$~ui', $item)) {
                    $item = '';
                }
            }
        }
        return $this;
    }

    /**
     * @param bool $inverseMMDD
     * @return $this
     */
    public function mapUnixTime($inverseMMDD = false)
    {
        $this->items = array_map(function($item) use ($inverseMMDD) {
            return DateHelper::parseDate($item, $inverseMMDD);
        }, $this->items);
        return $this;
    }

    /**
     * @param bool $removeExtra
     * @return $this
     */
    public function mapStates($removeExtra = true)
    {
        $states = [];
        foreach ($this->items as $item) {
            foreach (ParserHelper::parseStates($item, $removeExtra) as $k => $state) {
                if (is_int($k) && is_string($state)) {
                    $states[] = $state;
                }
            }
        }
        $this->items = $states;
        return $this;
    }
}
