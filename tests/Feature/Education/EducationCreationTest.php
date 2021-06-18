<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Education;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EducationCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_screen_can_be_rendered()
    {
        $this->userSigningIn();

        $response = $this->get('admin/educations/create');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_should_redirect_to_login_page()
    {
        $response = $this->get('admin/educations/create');
        $response->assertRedirect('login');

        $response = $this->post('admin/educations', $this->getDataFromFactory(Education::class));
        $response->assertRedirect('login');
    }

    public function test_users_can_store_valid_education_request_and_has_success_message()
    {
        $this->userSigningIn();

        $education = Education::first();
        $this->assertNull($education);

        $requestEducationData = $this->getDataFromFactory(Education::class);
        $response = $this->post('admin/educations', $requestEducationData);

        $educations = Education::all();

        $this->assertNotNull($educations);
        $this->assertEquals(1, $educations->count());

        $this->assertEquals($requestEducationData['institution'], $educations->first()->institution);
        $this->assertEquals($requestEducationData['degree'], $educations->first()->degree);
        $this->assertEquals($requestEducationData['start_period'], $educations->first()->start_period);
        $this->assertEquals($requestEducationData['end_period'], $educations->first()->end_period);

        $response->assertRedirect('admin/educations');
        $response->assertSessionHas(['status', 'messages']);
    }

    public function test_users_can_not_store_with_invalid_education_request()
    {
        $this->userSigningIn();

        $education = Education::first();

        $this->assertNull($education);

        $invalidEducationRequestData = [
            'institution' => null,
            'degree' => null,
            'start_period' => null,
        ];

        $response = $this->post('admin/educations', $invalidEducationRequestData);

        $educations = Education::all();

        $this->assertNotNull($educations);
        $this->assertEmpty($educations);
        $this->assertEquals(0, $educations->count());
    }

    public function test_users_should_get_error_messages_when_failed_to_store_data()
    {
        $this->userSigningIn();
        
        $invalidEducationData = [
            'institution' => null,
            'degree' => null,
            'start_period' => null,
            'end_period' => null
        ];

        $response = $this->post('admin/educations', $invalidEducationData);

        $response->assertSessionHasErrors(['institution', 'degree', 'start_period']);
    }
}
