<?php
namespace pheonixsearch\core;

class StdFields
{
    // time took to process in ms
    private $took    = 0;
    // index name
    private $index   = '';
    // index type
    private $type    = '';

    private $score   = 0.0;
    // if new document created
    private $created = true;
    // whether time is out and stop process by returning results
    private $timedOut = false;

    /**
     * @return int
     */
    public function getTook(): int
    {
        return $this->took;
    }

    /**
     * @param int $took
     */
    public function setTook(int $took)
    {
        $this->took = $took;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex(string $index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isCreated(): bool
    {
        return $this->created;
    }

    /**
     * @param bool $created
     */
    public function setCreated(bool $created)
    {
        $this->created = $created;
    }

    /**
     * @return bool
     */
    public function isTimedOut(): bool
    {
        return $this->timedOut;
    }

    /**
     * @param bool $timedOut
     */
    public function setTimedOut(bool $timedOut)
    {
        $this->timedOut = $timedOut;
    }
}