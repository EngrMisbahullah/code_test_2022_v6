<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;
use App\Helpers\TeHelper;
use Carbon\Carbon;

class ExpirationTest extends TestCase
{

    public function testWillExpireAtWithDifferenceLessThanOrEqual90()
    {
        $dueTime = Carbon::now()->addHours(90);
        $createdAt = Carbon::now();
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($dueTime->format('Y-m-d H:i:s'), $result);
    }

    public function testWillExpireAtWithDifferenceLessThanOrEqual24()
    {
        $dueTime = Carbon::now();
        $createdAt = Carbon::now()->subHours(24);
        $expected = $createdAt->copy()->addMinutes(90)->format('Y-m-d H:i:s');
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($expected, $result);
    }

    public function testWillExpireAtWithDifferenceBetween24And72()
    {
        $dueTime = Carbon::now()->addHours(16);
        $createdAt = Carbon::now()->subHours(48);
        $expected = $createdAt->copy()->addHours(16)->format('Y-m-d H:i:s');
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($expected, $result);
    }

    public function testWillExpireAtWithDifferenceGreaterThan72()
    {
        $dueTime = Carbon::now()->subHours(48);
        $createdAt = Carbon::now()->subHours(96);
        $expected = $dueTime->copy()->subHours(48)->format('Y-m-d H:i:s');
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($expected, $result);
    }

    public function testWillExpireAtWithExactDueTime()
    {
        $dueTime = Carbon::now()->addHours(48);
        $createdAt = Carbon::now()->subHours(48);
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($dueTime->format('Y-m-d H:i:s'), $result);
    }

    public function testWillExpireAtWithNegativeDifference()
    {
        $dueTime = Carbon::now()->subHours(48);
        $createdAt = Carbon::now()->addHours(48);
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($dueTime->format('Y-m-d H:i:s'), $result);
    }

    public function testWillExpireAtWithZeroDifference()
    {
        $dueTime = Carbon::now();
        $createdAt = Carbon::now();
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($dueTime->format('Y-m-d H:i:s'), $result);
    }

    public function testWillExpireAtWithLargeDifference()
    {
        $dueTime = Carbon::now()->addHours(240);
        $createdAt = Carbon::now()->subHours(720);
        $expected = $dueTime->copy()->subHours(48)->format('Y-m-d H:i:s');
        $result = TeHelper::willExpireAt($dueTime, $createdAt);
        $this->assertEquals($expected, $result);
    }
}
