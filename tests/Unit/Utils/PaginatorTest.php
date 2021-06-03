<?php

namespace Tests\Unit\Utils;

use App\Utils\Paginator;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Query\Builder;

class PaginatorTest extends TestCase
{
    public function test_must_return_closure()
    {
    	$this->assertTrue(is_callable(Paginator::paginateByOrderAttribute('testkey', 'asc')));
    }

    public function test_when_order_by_is_null_must_return_asc()
    {
    	$this->assertEquals('asc', Paginator::getOrderValue());
    }

    public function test_when_order_by_is_asc_must_return_asc()
    {
    	$this->assertEquals('asc', Paginator::getOrderValue('asc'));
    }

    public function test_when_order_by_is_desc_must_return_desc()
    {
    	$this->assertEquals('desc', Paginator::getOrderValue('desc'));
    }

    public function test_when_order_by_is_descending_must_return_desc()
    {
    	$this->assertEquals('desc', Paginator::getOrderValue('descending'));
    }

    public function test_when_order_by_is_random_value_must_return_asc()
    {
    	$this->assertEquals('asc', Paginator::getOrderValue('random-order'));
    }
}
