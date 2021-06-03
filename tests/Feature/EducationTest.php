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
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('admin/educations');

        $response->assertStatus(200);
    }

    public function test_education_detail_page_can_be_rendered()
    {
        $user = User::factory()->create();

        Education::factory()->create();

        $id = (Education::first())->id;
        $response = $this->actingAs($user)->get("admin/educations/{$id}");

        $response->assertStatus(200);
    }

    public function test_education_edit_page_can_be_rendered()
    {
        $user = User::factory()->create();

        Education::factory()->create();

        $id = (Education::first())->id;
        $response = $this->actingAs($user)->get("admin/educations/{$id}/edit");

        $response->assertStatus(200);
    }

    public function test_redirect_unauthenticated_user()
    {
        $response = $this->get('admin/educations');
        $response->assertRedirect('login');
    }

    public function test_data_was_paginatate_on_passed_data_to_view()
    {
        $this->withoutExceptionHandling();

        Education::factory()->count(20)->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('admin/educations');

        $expectedViewPaginationKeys = [
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

        $response->assertViewHasAll($expectedViewPaginationKeys);
        $response->assertViewIs('admin.education.index');
    }

    public function test_pagination_return_max_10_rows()
    {
        $this->withoutExceptionHandling();

        Education::factory()->count(20)->create();

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("admin/educations");

        $response->assertViewHas('per_page', 10);
    }

    public function test_pagination_limit_works_dynamically()
    {
        Education::factory()->count(20)->create();

        $user = User::factory()->create();
        $perPage = 5;
        $response = $this->actingAs($user)->get("admin/educations?limit={$perPage}");

        $response->assertViewHas('per_page', $perPage);
    }

    public function test_order_data_by_given_key_as_ascending()
    {
        $this->withoutExceptionHandling();

        $exampleGivenKey = 'institution';

        for ($i=9; $i > 0; $i--) { 
            Education::create([
                'institution' => '#' . $i,
                'degree' => $this->faker->word(),
                'start_period' => $this->faker->date(),
                'end_period' => $this->faker->date()
            ]);
        }

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("admin/educations?order_by={$exampleGivenKey}&order_as=ASC");

        $viewData = $response->viewData('data');

        $this->assertEquals('#1', $viewData[0]['institution']);
        $this->assertEquals('#2', $viewData[1]['institution']);
    }

    public function test_order_data_by_given_key_as_descending()
    {
        $this->withoutExceptionHandling();

        $exampleGivenKey = 'institution';

        for ($i=0; $i < 10; $i++) { 
            Education::create([
                'institution' => '#' . $i,
                'degree' => $this->faker->word(),
                'start_period' => $this->faker->date(),
                'end_period' => $this->faker->date()
            ]);
        }

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("admin/educations?order_by={$exampleGivenKey}&order_as=DESC");

        $viewData = $response->viewData('data');

        $this->assertEquals('#9', $viewData[0]['institution']);
        $this->assertEquals('#8', $viewData[1]['institution']);
    }

    public function test_return_only_a_row_on_detail_page()
    {
        $user = User::factory()->create();

        $institution = $this->faker->company();
        $degree = $this->faker->word();
        $start_period = $this->faker->date();
        $end_period = $this->faker->date();

        Education::create([
            'institution' => $institution,
            'degree' => $degree,
            'start_period' => $start_period,
            'end_period' => $end_period
        ]);

        $id = (Education::first())->id;
        $response = $this->actingAs($user)->get("admin/educations/{$id}");

        $response->assertStatus(200);
        $response->assertViewHas('education');

        $viewData = $response->viewData('education');

        $this->assertEquals(1, $viewData->count());
        $this->assertEquals($institution, $viewData->institution);
        $this->assertEquals($degree, $viewData->degree);
        $this->assertEquals($start_period, $viewData->start_period);
        $this->assertEquals($end_period, $viewData->end_period);
    }

    public function test_return_not_found_status_for_not_found_data()
    {
        $user = User::factory()->create();

        // detail page
        $detailPageResponse = $this->actingAs($user)->get("admin/educations/1");
        $detailPageResponse->assertNotFound();

        // editing page
        $editingPageResponse = $this->actingAs($user)->get("admin/educations/1/edit");
        $editingPageResponse->assertNotFound();
    }

}
