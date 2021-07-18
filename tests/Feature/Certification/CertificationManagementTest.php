<?php

namespace Tests\Feature\Certification;

use App\Models\Certification;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CertificationManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_should_see_paginated_certification_lists_on_index_page()
    {
        $this->userSigningIn();
        $this->withoutExceptionHandling();

        Certification::factory()->count(20)->create();

        $response = $this->get('admin/certifications');
        $viewData = $response->viewData('data');

        $certification = Certification::first();

        $response->assertOk();
        $this->assertPaginated($response);

        $this->assertEquals($viewData[0]['title'], $certification->title);
        $this->assertEquals($viewData[0]['topic'], $certification->topic);
        $this->assertEquals($viewData[0]['description'], $certification->description);
        $this->assertEquals($viewData[0]['start_period'], $certification->start_period);
        $this->assertEquals($viewData[0]['end_period'], $certification->end_period);
    }

    /** @test */
    public function user_can_add_new_certification()
    {
        $this->userSigningIn();

        $rowCountBeforeCreation = (Certification::all())->count();

        $certification = $this->getDataFromFactory(Certification::class, ['end_period' => $this->faker->date()]);

        $response = $this->post('admin/certifications', $certification);

        $rowCountAfterCreation = (Certification::all())->count();
        $createdCertification = Certification::first();

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHasAll(['messages', 'status']);

        $this->assertEquals(0, $rowCountBeforeCreation);
        $this->assertEquals(1, $rowCountAfterCreation);

        $this->assertEquals($certification['title'], $createdCertification->title);
        $this->assertEquals($certification['topic'], $createdCertification->topic);
        $this->assertEquals($certification['description'], $createdCertification->description);
        $this->assertEquals($certification['start_period'], $createdCertification->start_period);
        $this->assertEquals($certification['end_period'], $createdCertification->end_period);
    }

    /** @test */
    public function user_can_see_single_certificaition()
    {
        $this->userSigningIn();

        $certification = (Certification::factory()->create(
                ['end_period' => $this->faker->date()]
            ))->toArray();

        $response = $this->get("admin/certifications/{$certification['id']}");
        $viewData = $response->viewData('data');

        $createdCertification = Certification::first();

        $response->assertOk();

        $this->assertEquals($certification['title'], $createdCertification->title);
        $this->assertEquals($certification['topic'], $createdCertification->topic);
        $this->assertEquals($certification['description'], $createdCertification->description);
        $this->assertEquals($certification['start_period'], $createdCertification->start_period);
        $this->assertEquals($certification['end_period'], $createdCertification->end_period);
    }

    /** @test */
    public function user_can_edit_certification()
    {
        $this->userSigningIn();

        $certificationBeforeEdited = (Certification::factory()->create())->toArray();
        $certificationNewData = $this->getDataFromFactory(Certification::class, ['end_period' => $this->faker->date()]);

        $response = $this->put("admin/certifications/{$certificationBeforeEdited['id']}", $certificationNewData);

        $updatedCertification = Certification::first();

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHasAll(['messages', 'status']);

        $this->assertEquals($certificationNewData['title'], $updatedCertification->title);
        $this->assertEquals($certificationNewData['topic'], $updatedCertification->topic);
        $this->assertEquals($certificationNewData['description'], $updatedCertification->description);
        $this->assertEquals($certificationNewData['start_period'], $updatedCertification->start_period);
        $this->assertEquals($certificationNewData['end_period'], $updatedCertification->end_period);
    }

    /** @test */
    public function user_can_delete_a_certification()
    {
        $this->userSigningIn();
        $this->withoutExceptionHandling();

        $certification = (Certification::factory()->create())->toArray();
        $response = $this->delete("admin/certifications/{$certification['id']}");

        $rowsAfterDeleted = (Certification::all())->count();

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHasAll(['messages', 'status']);

        $this->assertEquals(0, $rowsAfterDeleted);
    }   
}
