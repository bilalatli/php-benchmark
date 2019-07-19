<?php

namespace BALib\Benchmark;

use BALib\Benchmark\Library\CheckPoint;
use RuntimeException;
use InvalidArgumentException;

/**
 * @author  : Bilal ATLI
 * @date    : 30.03.2019 15:44
 * @mail    : <bilal@garivaldi.com>, <ytbilalatli@gmail.com>
 * @phone   : +90 0-542-433-09-19
 *
 * @package BALib\Benchmark;
 */
class Benchmark
{
    /**
     * Benchmark Reserved Key
     *
     * @var string
     */
    private static $benchmarkReservedKey = "__benchmark__";

    /**
     * Check Points
     *
     * @var array
     */
    private static $checkPoints = [];

    /**
     * Benchmark is Running
     *
     * @var bool
     */
    private static $isActive = false;

    /**
     * Last Checkpoint Key
     *
     * @var string|null
     */
    private static $lastKey = null;

    /**
     * @var CheckPoint
     */
    private static $cp;

    /**
     * Start Benchmark
     *
     * @return void
     */
    public static function start(): void
    {
        if (self::$isActive) {
            throw new RuntimeException("Checkpoint is already started");
        }

        self::$cp = CheckPoint::getInstance();

        self::$cp->create(self::$benchmarkReservedKey);
        self::$checkPoints[self::$benchmarkReservedKey] = self::$cp->toArray();
        self::$isActive = true;
    }

    /**
     * Create New Checkpoint
     *
     * @param string $key
     */
    public static function open(string $key = ""): void
    {
        if (!self::$isActive) {
            throw new RuntimeException("Checkpoint is not started");
        }

        if ($key === self::$benchmarkReservedKey) {
            throw new InvalidArgumentException("Checkpoint key can not be equal reserved benchmark key");
        }

        self::$cp->create($key);
        self::$lastKey = self::$cp->getKey();
        self::$checkPoints[self::$cp->getKey()] = self::$cp->toArray();
    }

    /**
     * Close Specified Checkpoint
     *
     * @param string $key
     */
    public static function close(string $key = ""): void
    {
        if (!self::$isActive) {
            throw new RuntimeException("Checkpoint is not started");
        }

        $key = empty($key) ? ( self::$lastKey ) : $key;

        if (is_null($key) || !isset(self::$checkPoints[$key])) {
            throw new RuntimeException("Checkpoint key is not found");
        }

        if ($key === self::$benchmarkReservedKey) {
            throw new InvalidArgumentException("Checkpoint key can not be equal reserved benchmark key");
        }

        self::$cp->load(self::$checkPoints[$key]);
        self::$cp->stop();
        self::$checkPoints[$key] = self::$cp->toArray();
        self::$lastKey = null;
    }

    /**
     * Stop Benchmark
     *
     * @return void
     */
    public static function stop(): void
    {
        if (!self::$isActive) {
            throw new RuntimeException("Checkpoint is not started");
        }

        self::$isActive = false;
        self::$cp->load(self::$checkPoints[self::$benchmarkReservedKey])->stop();
        self::$checkPoints[self::$benchmarkReservedKey] = self::$cp->toArray();
    }

    /**
     * Get Checkpoint Information
     *
     * @param string $key
     *
     * @return CheckPoint
     */
    public static function getCheckpoint(string $key)
    {
        if (!isset(self::$checkPoints[$key])) {
            throw new RuntimeException("Checkpoint not exist");
        }

        $checkpoint = CheckPoint::getInstance();
        $checkpoint->reset();
        $checkpoint->load(self::$checkPoints[$key]);

        return $checkpoint;
    }

    /**
     * Get Checkpoints Count
     *
     * @return int
     */
    public static function count(): int
    {
        return count(self::$checkPoints);
    }

    /**
     * Get Values to Array
     *
     * @return array
     */
    public static function toArray(): array
    {
        $data = self::$checkPoints;
        $general = $data[self::$benchmarkReservedKey];
        unset($data[self::$benchmarkReservedKey]);
        return [
            'allocatedMemory'  => self::$cp->getMemoryUsage(true),
            'maxExecutionTime' => ini_get('max_execution_time'),
            'general'          => $general,
            'checkPoints'      => $data,
        ];
    }
}