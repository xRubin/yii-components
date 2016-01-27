<?php

/**
 * Class RedisSortedSetDataProvider
 */
class RedisSortedSetDataProvider extends CDataProvider
{
    /** @var \ARedisSortedSet */
    public $redisSet;

    /** @var bool */
    public $order = CSort::SORT_ASC;

    /**
     * @param \ARedisSortedSet $redisSet
     * @param array $config
     */
    public function __construct($redisSet, $config = [])
    {
        $this->redisSet = $redisSet;
        foreach ($config as $key => $value)
            $this->$key = $value;

        $this->getPagination()->setItemCount($this->getTotalItemCount());
    }

    /**
     * Fetches the data from the persistent data storage.
     * @return array list of data items
     */
    protected function fetchData()
    {
        if ($this->order === CSort::SORT_ASC) {
            return $this->redisSet->getConnection()->getClient()->zRange(
                $this->redisSet->name,
                $this->pagination->offset,
                $this->pagination->limit + $this->pagination->offset - 1,
                true
            );
        } else {
            return $this->redisSet->getConnection()->getClient()->zRevRange(
                $this->redisSet->name,
                $this->pagination->offset,
                $this->pagination->limit + $this->pagination->offset - 1,
                true
            );
        }
    }

    /**
     * @return array
     */
    protected function fetchKeys()
    {
        if ($this->order === CSort::SORT_ASC) {
            return $this->redisSet->getConnection()->getClient()->zRange(
                $this->redisSet->name,
                $this->pagination->offset,
                $this->pagination->limit + $this->pagination->offset - 1
            );
        } else {
            return $this->redisSet->getConnection()->getClient()->zRevRange(
                $this->redisSet->name,
                $this->pagination->offset,
                $this->pagination->limit + $this->pagination->offset - 1
            );
        }
    }

    /**
     * @return int
     */
    protected function calculateTotalItemCount()
    {
        return $this->redisSet->count();
    }
}
