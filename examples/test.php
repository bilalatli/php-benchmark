<?php
/**
 *
 * @author Bilal ATLI
 * Date: 19.07.2019
 * Time: 10:08
 * E-mail : <bilal@garivaldi.com>, <ytbilalatli@gmail.com>
 * Phone : +90 0-542-433-09-19
 * Original Filename : test.php
 */

use BALib\Benchmark\Benchmark;

require_once "vendor/autoload.php";

Benchmark::start();
Benchmark::open("step1");

for ($i = 0; $i <= 50; $i++) {
    echo "> Waiting 50ms [$i]" . PHP_EOL;
    usleep(50000);
}

Benchmark::close("step1");
Benchmark::stop();

dd(Benchmark::toArray(), time());