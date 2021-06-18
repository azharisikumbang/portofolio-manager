<?php

namespace Tests\Feature\Experience;

use App\Models\Experience;
use App\Utils\Paginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExperienceTest extends TestCase
{
	use RefreshDatabase;

    /** @test */
    public function user_can_see_experiences_in_experience_index_page()
    {
		$this->userSigningIn();
		$this->withoutExceptionHandling();

		Experience::factory()->count(20)->create();

		$response = $this->get("admin/experiences");

		$responseViewData = $response->viewData('data');
		$experience = Experience::first();

		$response->assertOk();
		$this->assertPaginated($response);
		$this->assertEquals(Paginator::OFFSET, count($responseViewData));
		$this->assertEquals($experience->company, $responseViewData[0]['company']);
		$this->assertEquals($experience->position, $responseViewData[0]['position']);
		$this->assertEquals($experience->details, $responseViewData[0]['details']);
		$this->assertEquals($experience->start_period, $responseViewData[0]['start_period']);
		$this->assertEquals($experience->end_period, $responseViewData[0]['end_period']);
    }

    /** @test */
    public function user_can_use_pagination_feature_to_experiences_in_experience_index_page()
    {
		$this->userSigningIn();
		$this->withoutExceptionHandling();

		$limit = 10;
		$orderBy = 'company';
		$orderAs = 'descending';

		$experiences = Experience::factory()->count(10)->make();
		$companies = $experiences->map(function($experience) use ($orderBy) {
			$experience->save();
			return $experience->{$orderBy};
		})->toArray();

		rsort($companies); // sort as descending

		$response = $this->get("admin/experiences?limit={$limit}&order_by={$orderBy}&order_as={$orderAs}");
		$responseViewData = $response->viewData('data');

		$response->assertOk();
		$this->assertPaginated($response, ['per_page' => $limit]);
		$this->assertEquals($limit, count($responseViewData));

		for ($i=0; $i < count($companies); $i++) { 
			$this->assertEquals($companies[$i], $responseViewData[$i][$orderBy]);
		}
    }
}