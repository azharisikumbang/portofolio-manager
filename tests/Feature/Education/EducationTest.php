<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Education;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_education_index_page_can_be_rendered()
    {
        $this->userSigningIn();
        $response = $this->get('admin/educations');

        $response->assertStatus(200);
    }

    public function test_education_detail_page_can_be_rendered()
    {
        $this->userSigningIn();

        Education::factory()->create();

        $id = (Education::first())->id;
        $response = $this->get("admin/educations/{$id}");

        $response->assertStatus(200);
    }

    public function test_education_edit_page_can_be_rendered()
    {
        $this->userSigningIn();

        Education::factory()->create();

        $id = (Education::first())->id;
        $response = $this->get("admin/educations/{$id}/edit");

        $response->assertStatus(200);
    }

    public function test_users_should_be_redirected_when_unauthenticated()
    {
        $response = $this->get('admin/educations');
        $response->assertRedirect('login');
    }

    public function test_users_should_get_paginated_data()
    {
        $this->userSigningIn();
        
        Education::factory()->count(20)->create();
        $response = $this->get('admin/educations');

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

        $response->assertViewHasAll($paginationKeys);
        $response->assertViewIs('admin.education.index');
    }

    public function test_users_should_get_max_10_rows_as_default_on_paginated_page()
    {
        Education::factory()->count(20)->create();

        $this->userSigningIn();
        $response = $this->get("admin/educations");

        $response->assertViewHas('per_page', 10);
    }

    public function test_users_can_set_pagination_limit_dynamically()
    {
        Education::factory()->count(20)->create();

        $this->userSigningIn();
        $perPage = 5;
        $response = $this->get("admin/educations?limit={$perPage}");

        $response->assertViewHas('per_page', $perPage);
    }

    public function test_users_can_order_by_given_key()
    {
        $exampleGivenKey = 'institution';

        for ($i=9; $i > 0; $i--) { 
            Education::create([
                'institution' => '#' . $i,
                'degree' => $this->faker->word(),
                'start_period' => $this->faker->date(),
                'end_period' => $this->faker->date()
            ]);
        }

        $this->userSigningIn();
        $response = $this->get("admin/educations?order_by={$exampleGivenKey}");

        $viewData = $response->viewData('data');

        $this->assertEquals('#1', $viewData[0]['institution']);
        $this->assertEquals('#2', $viewData[1]['institution']);
    }

    public function test_users_can_order_by_given_key_as_descending()
    {
        $exampleGivenKey = 'institution';

        for ($i=9; $i > 0; $i--) { 
            Education::create([
                'institution' => '#' . $i,
                'degree' => $this->faker->word(),
                'start_period' => $this->faker->date(),
                'end_period' => $this->faker->date()
            ]);
        }

        $this->userSigningIn();
        $response = $this->get("admin/educations?order_by={$exampleGivenKey}&order_as=ASC");

        $viewData = $response->viewData('data');

        $this->assertEquals('#1', $viewData[0]['institution']);
        $this->assertEquals('#2', $viewData[1]['institution']);
    }

    public function test_users_can_order_data_by_given_key_as_descending()
    {
        $exampleGivenKey = 'institution';

        for ($i=0; $i < 10; $i++) { 
            Education::create([
                'institution' => '#' . $i,
                'degree' => $this->faker->word(),
                'start_period' => $this->faker->date(),
                'end_period' => $this->faker->date()
            ]);
        }

        $this->userSigningIn();
        $response = $this->get("admin/educations?order_by={$exampleGivenKey}&order_as=DESC");

        $viewData = $response->viewData('data');

        $this->assertEquals('#9', $viewData[0]['institution']);
        $this->assertEquals('#8', $viewData[1]['institution']);
    }

    public function test_users_only_retrive_a_row_on_detail_page()
    {
        $this->userSigningIn();

        $education = $this->getDataFromFactory(Education::class);

        Education::create($education);

        $id = (Education::first())->id;
        $response = $this->get("admin/educations/{$id}");

        $response->assertStatus(200);
        $response->assertViewHas('education');

        $viewData = $response->viewData('education');

        $this->assertEquals(1, $viewData->count());
        $this->assertEquals($education['institution'], $viewData->institution);
        $this->assertEquals($education['degree'], $viewData->degree);
        $this->assertEquals($education['start_period'], $viewData->start_period);
        $this->assertEquals($education['end_period'], $viewData->end_period);
    }

    public function test_users_should_get_not_found_on_unavailable_data()
    {
        $this->userSigningIn();

        // detail page
        $detailPageResponse = $this->get("admin/educations/1");
        $detailPageResponse->assertNotFound();

        // editing page
        $editingPageResponse = $this->get("admin/educations/1/edit");
        $editingPageResponse->assertNotFound();
    }

}
