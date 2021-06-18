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
		$this->assertEquals($experience->detail, $responseViewData[0]['detail']);
		$this->assertEquals($experience->start_period, $responseViewData[0]['start_period']);
		$this->assertEquals($experience->end_period, $responseViewData[0]['end_period']);
    }

    /** @test */
    public function user_can_use_pagination_feature_to_experiences_in_experience_index_page()
    {
		$this->userSigningIn();

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

    /** @test */
    public function user_can_create_an_experience()
    {
		$this->userSigningIn();

		$this->assertEquals(0, (Experience::all())->count());

		$response = $this->post('admin/experiences', $this->getDataFromFactory(Experience::class));

		$response->assertRedirect('admin/experiences');
		$this->assertEquals(1, (Experience::all())->count());

		$response->assertSessionHasAll(['status', 'messages']);
		$response->assertSessionHasNoErrors();
    }

    /** @test */
    public function user_should_redirected_back_with_error_messages_when_failed_to_create_an_experience()
    {
		$this->userSigningIn();

		$response = $this->post('admin/experiences', [
		  	'company' => null,
		  	'position' => null,
		  	'detail' => null,
		  	'start_period' => null
		]);

		$this->assertEquals(0, (Experience::all())->count());
		$response->assertRedirect();
		$response->assertSessionHasErrors(['company', 'position', 'detail', 'start_period']);
		$response->assertSessionDoesntHaveErrors('end_period');
    }

    /** @test */
    public function user_can_update_an_experience()
    {
    	$this->userSigningIn();

    	Experience::factory()->create();

    	$experience = Experience::first();

    	$newExperienceData = [
    		'company' => 'a new company',
    		'position' => 'a new position',
    		'detail' => 'some detail about the experience',
    		'start_period' => date('Y/m/d'),
    		'end_period' => date('Y/m/d')
    	];

    	$response = $this->put("admin/experiences/{$experience->id}", $newExperienceData);
    	$updatedExperience = Experience::first();

    	$this->assertEquals(1, (Experience::all())->count());
    	$this->assertEquals($newExperienceData['company'], $updatedExperience->company);
    	$this->assertEquals($newExperienceData['position'], $updatedExperience->position);
    	$this->assertEquals($newExperienceData['detail'], $updatedExperience->detail);
    	$this->assertEquals($newExperienceData['start_period'], $updatedExperience->start_period);

    	$response->assertRedirect('admin/experiences');
    	$response->assertSessionHasNoErrors();
    	$response->assertSessionHasAll(['status', 'messages']);
    }

    /** @test */
    public function user_can_delete_an_experience()
    {
    	$this->userSigningIn();

    	Experience::factory()->create();
		$this->assertEquals(1, (Experience::all())->count());

    	$experience = Experience::first();

    	$response = $this->delete("admin/experiences/{$experience->id}");

		$this->assertEquals(0, (Experience::all())->count());
    	$response->assertRedirect('admin/experiences');
    	$response->assertSessionHasNoErrors();
    }    
}