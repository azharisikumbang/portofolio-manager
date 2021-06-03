<?php 

namespace App\Utils;

use Illuminate\Database\Eloquent\Builder;

class Paginator 
{
	public const OFFSET = 10;

	public static function getOrderValue(?string $orderAs = null) : string
	{	
		if (is_null($orderAs)) {
			return 'asc';
		}

		$orderAs = strtolower($orderAs);

		switch ($orderAs) {
			case 'desc':
			case 'descending':
				$orderAs = 'desc';
				break;
			
			default:
				$orderAs = 'asc';
				break;
		}

		return $orderAs;
	}

	public static function paginateByOrderAttribute(string $orderBy, ?string $orderAs = null) : \Closure
	{
		return function ($query) use ($orderBy, $orderAs) {
            return $query->orderBy(
            	$orderBy, 
            	static::getOrderValue($orderAs)
            );
        };
	}
}