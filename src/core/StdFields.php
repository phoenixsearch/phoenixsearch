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
    // internal _id for output on indexing
    private $id = 0;

    private $timestamp = 0;
    // total docs found
    private $total   = 0;

    private $score   = 0.0;
    // if new document created
    private $created = true;
    // whether time is out and stop process by returning results
    private $timedOut = false;
    // json options for output ex.: pretty print
    private $opts = 0;
    // source doc(s) mined by search or inserted by index
    private $hits = [];
    // resulting operation
    private $result = '';
    // operation type
    private $opType = '';
    // operation status
    private $opStatus = false;
    // version of document
    private $version = 1;
    // bit mask with OP_TYPE, TOOK, INDEX, TYPE, ID set by default
    private $fieldsBitMask = 0b000011111;

    private $source = [];

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

    /**
     * @return int
     */
    public function getOpts(): int
    {
        return $this->opts;
    }

    /**
     * @param int $opts
     */
    public function setOpts(int $opts)
    {
        $this->opts = $opts;
    }

    /**
     * @return array
     */
    public function getHits(): array
    {
        return $this->hits;
    }

    /**
     * @param array $hits
     */
    public function setHits(array $hits)
    {
        $this->hits = $hits;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getOpType(): string
    {
        return $this->opType;
    }

    /**
     * @param string $opType
     */
    public function setOpType(string $opType)
    {
        $this->opType = $opType;
    }

    /**
     * @return bool
     */
    public function isOpStatus(): bool
    {
        return $this->opStatus;
    }

    /**
     * @param bool $opStatus
     */
    public function setOpStatus(bool $opStatus)
    {
        $this->opStatus = $opStatus;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version)
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getFieldsBitMask(): int
    {
        return $this->fieldsBitMask;
    }

    /**
     * @param int $bit
     */
    public function setFieldsBitMask(int $bit)
    {
        $this->fieldsBitMask |= $bit;
    }

    /**
     * @param int $bit
     */
    public function unsetFieldsBitMask(int $bit)
    {
        $this->fieldsBitMask &= ~$bit;
    }

    /**
     * @param int $bits
     *
     * @return bool
     */
    public function isBitSet(int $bits): bool
    {
        return $this->fieldsBitMask & $bits;
    }

    /**
     * @return array
     */
    public function getSource(): array
    {
        return $this->source;
    }

    /**
     * @param array $source
     */
    public function setSource(array $source)
    {
        $this->source = $source;
    }
}