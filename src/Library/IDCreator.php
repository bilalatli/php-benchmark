<?php

namespace BALib\Benchmark\Library;

use Exception;
use InvalidArgumentException;

/**
 * @author  : Bilal ATLI
 * @date    : 30.03.2019 15:44
 * @mail    : <bilal@garivaldi.com>, <ytbilalatli@gmail.com>
 * @phone   : +90 0-542-433-09-19
 *
 * @package BAlib\Benchmark\Library;
 */
class IDCreator
{
    /**
     * Get Unique ID For Benchmark Checkpoint
     *
     * @param int $suffixLength
     *
     * @return string
     */
    public static function uniqueID(int $suffixLength = 32): string
    {
        $timeHex = self::timeToHex();
        if ($suffixLength <= strlen($timeHex)) {
            $timeHex = "";
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $random = md5(openssl_random_pseudo_bytes($suffixLength));
        } else if (function_exists('random_bytes')) {
            try {
                $random = md5(random_bytes($suffixLength));
            } catch (Exception $e) {
                $random = self::randomHash($suffixLength);
            }
        } else {
            $random = self::randomHash($suffixLength);
        }

        return $timeHex . '/' . $random;
    }

    /**
     * Random Hash Generate
     *
     * @param int $length
     *
     * @return string
     */
    private static function randomHash(int $length): string
    {
        if ($length <= 0) {
            throw new InvalidArgumentException("Length must be greater than zero");
        }

        $hash = '';

        // Each To Hash Length
        for ($i = 0; $i <= $length; $i++) {
            $rangeBlock = rand(0, 2);
            if ($rangeBlock === 0) {
                $byte = rand(96, 121);
            } else if ($rangeBlock === 1) {
                $byte = rand(64, 89);
            } else {
                $byte = rand(47, 56);
            }

            $hash .= chr($byte);
        }

        return $hash;
    }

    /**
     * Get Time to Hex String
     *
     * @return string
     */
    private static function timeToHex(): string
    {
        $timestamp = microtime(true);
        return dechex($timestamp);
    }
}