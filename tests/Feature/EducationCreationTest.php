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

    private function _get_sample_data()
    {
        return Education::factory()->create()->toArray();
    }

    private function _get_defined_data()
    {
        return [
            'institution' => 'An institution',
            'degree' => 'Bachelor Degree',
            'start_period' => date('2015-01-01'),
            'end_period' => date('2020-01-01')
        ];
    }

    private function _actor()
    {
        return User::factory()->create();
    }

    public function test_create_screen_can_be_rendered()
    {
        $response = $this->actingAs($this->_actor())
                        ->get('admin/educations/create');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_should_redirect_to_login_page()
    {
        $response = $this->get('admin/educations/create');
        $response->assertRedirect('login');

        $response = $this->post('admin/educations', $this->_get_sample_data());
        $response->assertRedirect('login');
    }

    public function test_users_can_stote_valid_values()
    {
        $freshRowOnDatabase = Education::first();

        $this->assertNull($freshRowOnDatabase);

        $definedSampleData = $this->_get_defined_data();

        $response = $this->actingAs($this->_actor())
                        ->post('admin/educations', $definedSampleData);

        $rowOnDatabase = Education::all();

        $this->assertNotNull($rowOnDatabase);
        $this->assertEquals(1, $rowOnDatabase->count());

        $this->assertEquals($definedSampleData['institution'], $rowOnDatabase[0]->institution);
        $this->assertEquals($definedSampleData['degree'], $rowOnDatabase[0]->degree);
        $this->assertEquals($definedSampleData['start_period'], $rowOnDatabase[0]->start_period);
        $this->assertEquals($definedSampleData['end_period'], $rowOnDatabase[0]->end_period);

        $response->assertRedirect('admin/educations');
    }

    public function test_users_can_not_store_with_invalid_values()
    {
        $freshRowOnDatabase = Education::first();

        $this->assertNull($freshRowOnDatabase);

        $invalidSampleData = [
            'institution' => null,
            'degree' => null,
            'start_period' => null,
        ];

        $response = $this->actingAs($this->_actor())
                        ->post('admin/educations', $invalidSampleData);

        $rowOnDatabase = Education::all();

        $this->assertNotNull($rowOnDatabase);
        $this->assertEmpty($rowOnDatabase);
        $this->assertEquals(0, $rowOnDatabase->count());
    }

    public function test_users_should_get_error_messages_when_failed_to_store_data()
    {
        $invalidSampleData = [
            'institution' => null,
            'degree' => null,
            'start_period' => null,
            'end_period' => null
        ];

        $response = $this->actingAs($this->_actor())
                        ->post('admin/educations', $invalidSampleData);

        $response->assertSessionHasErrors(['institution', 'degree', 'start_period']);
    }
}
