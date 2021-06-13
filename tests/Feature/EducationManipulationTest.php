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

	public function test_unauthenticated_users_should_be_redirect_to_login_page()
	{
		$response = $this->put("admin/educations/1", $this->getDataFromFactory(Education::class));

		$response->assertRedirect('/login');
		$this->assertGuest();
	}

	public function test_users_can_update_the_data_and_has_success_message()
	{
		$this->userSigningIn();
		Education::factory()->create();

		$editEducationRequestData = $this->getDataFromFactory(Education::class);

		$existingEducationId = (Education::first())->id; 

		$response = $this->put("admin/educations/${existingEducationId}", $editEducationRequestData);

		$response->assertRedirect("admin/educations/{$existingEducationId}");
		$response->assertSessionHas('status');
		$response->assertSessionHasNoErrors();

		$updatedRow = Education::find($existingEducationId);

		$this->assertNotNull($updatedRow);

		$this->assertEquals($editEducationRequestData['institution'], $updatedRow->institution);
		$this->assertEquals($editEducationRequestData['degree'], $updatedRow->degree);
		$this->assertEquals($editEducationRequestData['start_period'], date('Y-m-d', strtotime($updatedRow->start_period)) );
		$this->assertEquals($editEducationRequestData['end_period'], date('Y-m-d', strtotime($updatedRow->end_period)) );
	}

	public function test_users_can_not_update_data_with_invalid_request_values_and_redirect_with_error_messages()
	{	
		$this->userSigningIn();

		$education = $this->getDataFromFactory(Education::class);

		Education::create($education);

		$invalidEducationRequestData = [
			'institution' => null,
            'degree' => null,
            'start_period' => null,
		];

		$existingEducationId = (Education::first())->id; 

		$response = $this->put("admin/educations/${existingEducationId}", $invalidEducationRequestData);

		$response->assertRedirect();
		$response->assertSessionHasErrors(['institution', 'degree', 'start_period']);

		$updatedEducationData = Education::find($existingEducationId);

		$this->assertNotNull($updatedEducationData);

		$this->assertEquals($education['institution'], $updatedEducationData->institution);
		$this->assertEquals($education['degree'], $updatedEducationData->degree);
		$this->assertEquals($education['start_period'], date('Y-m-d', strtotime($updatedEducationData->start_period)) );
		$this->assertEquals($education['end_period'], date('Y-m-d', strtotime($updatedEducationData->end_period)) );
	}

	public function test_users_can_not_update_unavailable_row()
	{
		$this->userSigningIn();

		$response = $this->put("admin/educations/1", $this->getDataFromFactory(Education::class));

		$response->assertNotFound();

		$educations = Education::all();

		$this->assertEmpty($educations);
	}

}
