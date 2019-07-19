<?php

namespace BALib\Benchmark\Library;


use BALib\Benchmark\Traits\Singleton;
use JsonSerializable;
use Serializable;
use InvalidArgumentException;

/**
 * @author  : Bilal ATLI
 * @date    : 30.03.2019 15:44
 * @mail    : <bilal@garivaldi.com>, <ytbilalatli@gmail.com>
 * @phone   : +90 0-542-433-09-19
 *
 * @package BAlib\Benchmark\Library;
 */
class CheckPoint implements Serializable, JsonSerializable
{
    use Singleton;

    /**
     * @var string
     */
    private $key = "";

    /**
     * @var float|null
     */
    private $startMemory = null, $stopMemory = null;

    /**
     * @var float|null
     */
    private $startPeakMemory = null, $stopPeakMemory = null;

    /**
     * @var float|null
     */
    private $startTime = null, $stopTime = null;

    /**
     * @var int|null
     */
    private $timeDiff = null;

    /**
     * @var int|null
     */
    private $memoryDiff = null, $peakMemoryDiff = null;

    /**
     * Create New Checkpoint Data
     *
     * @param string $key
     *
     * @return CheckPoint
     */
    public function create(string $key = ""): CheckPoint
    {
        if (empty($key)) {
            $key = IDCreator::uniqueID(32);
        }

        $this->reset();
        $this->setKey($key)
            ->setStartTime($this->getMicroTime())
            ->setStartMemory($this->getMemoryUsage())
            ->setStartPeakMemory($this->getPeakMemoryUsage());

        return $this;
    }

    /**
     * Stop Checkpoint
     *
     * @return CheckPoint
     */
    public function stop(): CheckPoint
    {
        $this->setStopTime($this->getMicroTime())
            ->setStopMemory($this->getMemoryUsage())
            ->setStopPeakMemory($this->getPeakMemoryUsage());

        return $this;
    }

    /**
     * Checkpoint is Running ?
     *
     * @return bool
     */
    public function isRunning()
    {
        return !( $this->timeDiff === null && $this->memoryDiff === null && $this->peakMemoryDiff === null );
    }

    /**
     * Reset Checkpoint Data
     */
    public function reset(): void
    {
        $this->setStartMemory(null)
            ->setStopMemory(null)
            ->setStartPeakMemory(null)
            ->setStopPeakMemory(null)
            ->setStartTime(null)
            ->setStopTime(null)
            ->setMemoryDiff(null)
            ->setPeakMemoryDiff(null)
            ->setTimeDiff(null);
    }

    /**
     * Load Checkpoint Data
     *
     * @param array $checkPointData
     *
     * @return CheckPoint
     */
    public function load(array $checkPointData): CheckPoint
    {
        if (
            !array_key_exists('key', $checkPointData) ||
            !array_key_exists('startTime', $checkPointData) ||
            !array_key_exists('stopTime', $checkPointData) ||
            !array_key_exists('timeDiff', $checkPointData) ||
            !array_key_exists('startMemory', $checkPointData) ||
            !array_key_exists('stopMemory', $checkPointData) ||
            !array_key_exists('memoryDiff', $checkPointData) ||
            !array_key_exists('startPeakMemory', $checkPointData) ||
            !array_key_exists('stopPeakMemory', $checkPointData) ||
            !array_key_exists('peakMemoryDiff', $checkPointData)
        ) {
            throw new InvalidArgumentException("Invalid Checkpoint Data");
        }

        $this->setKey($checkPointData['key']);
        $this->setStartTime($checkPointData['startTime']);
        $this->setStopTime($checkPointData['stopTime']);
        $this->setTimeDiff($checkPointData['timeDiff']);
        $this->setStartMemory($checkPointData['startMemory']);
        $this->setStopMemory($checkPointData['stopMemory']);
        $this->setMemoryDiff($checkPointData['memoryDiff']);
        $this->setStartPeakMemory($checkPointData['startPeakMemory']);
        $this->setStopPeakMemory($checkPointData['stopPeakMemory']);
        $this->setPeakMemoryDiff($checkPointData['peakMemoryDiff']);

        return $this;
    }

    /**
     * Get Key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set Key
     *
     * @param string $key
     *
     * @return CheckPoint
     */
    private function setKey(string $key): CheckPoint
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get Start Memory
     *
     * @return float|null
     */
    public function getStartMemory(): ?float
    {
        return $this->startMemory;
    }

    /**
     * Set Start Memory
     *
     * @param float|null $startMemory
     *
     * @return CheckPoint
     */
    private function setStartMemory(?float $startMemory = null): CheckPoint
    {
        $this->startMemory = $startMemory;
        return $this;
    }

    /**
     * Get Stop Memory
     *
     * @return float|null
     */
    public function getStopMemory(): ?float
    {
        return $this->stopMemory;
    }

    /**
     * Set Stop Memory
     *
     * @param float|null $stopMemory
     *
     * @return CheckPoint
     */
    private function setStopMemory(?float $stopMemory = null): CheckPoint
    {
        $this->stopMemory = $stopMemory;

        if ($this->getStartMemory() !== null && $this->getStopMemory() !== null) {
            $this->setMemoryDiff($this->getStopMemory() - $this->getStartMemory());
        }

        return $this;
    }

    /**
     * Get Start Peak Memory
     *
     * @return float|null
     */
    public function getStartPeakMemory(): ?float
    {
        return $this->startPeakMemory;
    }

    /**
     * Set Start Peak Memory
     *
     * @param float|null $startPeakMemory
     *
     * @return CheckPoint
     */
    private function setStartPeakMemory(?float $startPeakMemory = null): CheckPoint
    {
        $this->startPeakMemory = $startPeakMemory;
        return $this;
    }

    /**
     * Get Stop Peak Memory
     *
     * @return float|null
     */
    public function getStopPeakMemory(): ?float
    {
        return $this->stopPeakMemory;
    }

    /**
     * Set Stop Peak Memory
     *
     * @param float|null $stopPeakMemory
     *
     * @return CheckPoint
     */
    private function setStopPeakMemory(?float $stopPeakMemory = null): CheckPoint
    {
        $this->stopPeakMemory = $stopPeakMemory;

        if ($this->getStartPeakMemory() !== null && $this->getStopPeakMemory() !== null) {
            $this->setPeakMemoryDiff($this->getStopPeakMemory() - $this->getStartPeakMemory());
        }

        return $this;
    }

    /**
     * Get Start Time
     *
     * @return float|null
     */
    public function getStartTime(): ?float
    {
        return $this->startTime;
    }

    /**
     * Set Start Time
     *
     * @param float |null $startTime
     *
     * @return CheckPoint
     */
    private function setStartTime(?float $startTime = null): CheckPoint
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * Get Stop Time
     *
     * @return float |null
     */
    public function getStopTime(): ?float
    {
        return $this->stopTime;
    }

    /**
     * Set Stop Time
     *
     * @param float |null $stopTime
     *
     * @return CheckPoint
     */
    private function setStopTime(?float $stopTime = null): CheckPoint
    {
        $this->stopTime = $stopTime;

        if ($this->getStartTime() !== null && $this->getStopTime() !== null) {
            $this->setTimeDiff($this->getStopTime() - $this->getStartTime());
        }

        return $this;
    }

    /**
     * Get Time Difference
     *
     * @return float|null
     */
    public function getTimeDiff(): ?float
    {
        return $this->timeDiff;
    }

    /**
     * Set Time Difference
     *
     * @param float|null $timeDiff
     *
     * @return CheckPoint
     */
    private function setTimeDiff(?float $timeDiff = null): CheckPoint
    {
        $this->timeDiff = $timeDiff;

        return $this;
    }

    /**
     * Get Memory Difference
     *
     * @return int|null
     */
    public function getMemoryDiff(): ?int
    {
        return $this->memoryDiff;
    }

    /**
     * Set Memory Difference
     *
     * @param int|null $memoryDiff
     *
     * @return CheckPoint
     */
    private function setMemoryDiff(?int $memoryDiff = null): CheckPoint
    {
        $this->memoryDiff = $memoryDiff;

        return $this;
    }

    /**
     * Get Peak Memory Difference
     *
     * @return int|null
     */
    public function getPeakMemoryDiff(): ?int
    {
        return $this->peakMemoryDiff;
    }

    /**
     * Set Peak Memory Difference
     *
     * @param int|null $peakMemoryDiff
     *
     * @return CheckPoint
     */
    private function setPeakMemoryDiff(?int $peakMemoryDiff = null): CheckPoint
    {
        $this->peakMemoryDiff = $peakMemoryDiff;

        return $this;
    }

    /**
     * Get Micro Time
     *
     * @return float
     */
    public function getMicroTime(): float
    {
        return microtime(true);
    }

    /**
     * Get Memory Usage
     *
     * @param bool $allocated
     *
     * @return float
     */
    public function getMemoryUsage(bool $allocated = false): float
    {
        return memory_get_usage($allocated);
    }

    /**
     * Get Peak Memory Usage
     *
     * @param bool $allocated
     *
     * @return float
     */
    public function getPeakMemoryUsage(bool $allocated = false): float
    {
        return memory_get_peak_usage($allocated);
    }

    /**
     * Get Values To Array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'key'             => $this->key,
            'startTime'       => $this->startTime,
            'stopTime'        => $this->stopTime,
            'timeDiff'        => $this->timeDiff,
            'startMemory'     => $this->startMemory,
            'stopMemory'      => $this->stopMemory,
            'memoryDiff'      => $this->memoryDiff,
            'startPeakMemory' => $this->startPeakMemory,
            'stopPeakMemory'  => $this->stopPeakMemory,
            'peakMemoryDiff'  => $this->peakMemoryDiff,
        ];
    }

    /**
     * String representation of object
     *
     * @link  https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Constructs the object
     *
     * @link  https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->key,
            $this->startTime,
            $this->stopTime,
            $this->timeDiff,
            $this->startMemory,
            $this->stopMemory,
            $this->memoryDiff,
            $this->startPeakMemory,
            $this->stopPeakMemory,
            $this->peakMemoryDiff
            ) = unserialize($serialized);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}