<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

   	protected function getActor()
   	{
   		return User::factory()->create();
   	}

   	protected function userSigningIn()
   	{
   		$user = $this->getActor();
   		$this->actingAs($user);
   		
   		return $user; 
   	}

      protected function getDataFromFactory(string $model, array $modifier = [], int $count = 1)
      {
         if (!class_exists($model)) {
            throw new ModelNotFoundException("Failed to create sample Data, model class $model doesn't exists.", 1);
         }

         if (!method_exists($model, 'factory')) {
            throw new Exception("{$model} does not implement factory.", 1);
         }

         $results = $model::factory()->count($count)->make()->toArray();

         if ($modifier) {
            foreach ($results as $key => $result) {
               $results[$key] = array_merge($result, $modifier);
            }
         }

         return $count > 1 ? $results : $results[0];
      }

      protected function assertPaginated(TestResponse $response, ?array $config = null) 
      {
         $paginationKeys = [
            'current_page',
            'data',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ];

        if ($config) {
           foreach ($config as $key => $value) {
              if (in_array(strtolower($key), $paginationKeys)) {
                 $response->assertViewHas($key, $value);
              }
           }
        }

        $response->assertViewHasAll($paginationKeys);
      }
}
