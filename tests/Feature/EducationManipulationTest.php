<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Education;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EducationManipulationTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	private function _get_actor()
	{
		return User::factory()->create();
	}

	public function test_unauthenticated_users_should_be_redirect_to_login_page()
	{
		$response = $this->put("admin/educations/1", [
			'institution' => 'An institution',
            'degree' => 'Bachelor Degree',
            'start_period' => date('2015-01-01'),
            'end_period' => date('2020-01-01')
		]);

		$response->assertRedirect('/login');
		$this->assertGuest();

	}

	public function test_users_can_update_the_data_and_has_success_message()
	{
		$user = $this->_get_actor();
		Education::factory()->create();

		$newEducationData = [
			'institution' => 'An institution',
            'degree' => 'Bachelor Degree',
            'start_period' => date('2015-01-01'),
            'end_period' => date('2020-01-01')
		];

		$existingEducationId = (Education::first())->id; 

		$response = $this->actingAs($user)->put("admin/educations/${existingEducationId}", $newEducationData);

		$response->assertRedirect("admin/educations/{$existingEducationId}");
		$response->assertSessionHas('status');
		$response->assertSessionHasNoErrors();

		$updatedRow = Education::find($existingEducationId);

		$this->assertNotNull($updatedRow);

		$this->assertEquals($newEducationData['institution'], $updatedRow->institution);
		$this->assertEquals($newEducationData['degree'], $updatedRow->degree);
		$this->assertEquals($newEducationData['start_period'], date('Y-m-d', strtotime($updatedRow->start_period)) );
		$this->assertEquals($newEducationData['end_period'], date('Y-m-d', strtotime($updatedRow->end_period)) );
	}

	public function test_users_can_not_update_data_with_invalid_request_values_and_redirect_with_error_messages()
	{	
		$user = $this->_get_actor();

		$definedData = [
			'institution' => 'An institution',
            'degree' => 'Bachelor Degree',
            'start_period' => date('2015-01-01'),
            'end_period' => date('2020-01-01')
		];

		Education::create($definedData);

		$newEducationData = [
			'institution' => null,
            'degree' => null,
            'start_period' => null,
		];

		$existingEducationId = (Education::first())->id; 

		$response = $this->actingAs($user)->put("admin/educations/${existingEducationId}", $newEducationData);

		$response->assertRedirect();
		$response->assertSessionHasErrors(['institution', 'degree', 'start_period']);

		$updatedRow = Education::find($existingEducationId);

		$this->assertNotNull($updatedRow);

		$this->assertEquals($definedData['institution'], $updatedRow->institution);
		$this->assertEquals($definedData['degree'], $updatedRow->degree);
		$this->assertEquals($definedData['start_period'], date('Y-m-d', strtotime($updatedRow->start_period)) );
		$this->assertEquals($definedData['end_period'], date('Y-m-d', strtotime($updatedRow->end_period)) );
	}

	public function test_users_can_not_update_unavailable_row()
	{
		$user = $this->_get_actor();

		$response = $this->actingAs($user)->put("admin/educations/1", [
			'institution' => 'An institution',
            'degree' => 'Bachelor Degree',
            'start_period' => date('2015-01-01'),
            'end_period' => date('2020-01-01')
		]);

		$response->assertNotFound();

		$educations = Education::all();

		$this->assertEmpty($educations);
	}

}
