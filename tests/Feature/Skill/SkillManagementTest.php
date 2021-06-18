<?php

namespace Tests\Feature\Skill;

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SkillManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_should_see_paginated_skills_on_index_page()
    {
        $this->userSigningIn();
        $this->withoutExceptionHandling();

        Skill::factory()->count(20)->create();

        $response = $this->get('admin/skills');
        $viewData = $response->viewData('data');
        $skill = Skill::first(); 

        $response->assertOk();
        $this->assertPaginated($response);

        $this->assertEquals($skill->title, $viewData[0]['title']);
        $this->assertEquals($skill->description, $viewData[0]['description']);
        $this->assertEquals($skill->level, $viewData[0]['level']);
    }

    /** @test */
    public function user_can_use_pagination_feature()
    {
        $this->userSigningIn();

        $limit = 10;
        $orderBy = 'title';
        $orderAs = 'descending';

        $skills = Skill::factory()->count(20)->make();
        $skillTitle = $skills->map(function($skill) use ($orderBy) {
            $skill->save();
            return $skill->{$orderBy};
        })->toArray();

        rsort($skillTitle); // sort as descending

        $response = $this->get("admin/skills?limit={$limit}&order_by={$orderBy}&order_as={$orderAs}");
        $responseViewData = $response->viewData('data');

        $response->assertOk();
        $this->assertPaginated($response, ['per_page' => $limit]);
        $this->assertEquals($limit, count($responseViewData));

        for ($i=0; $i < count($responseViewData); $i++) { 
            $this->assertEquals($skillTitle[$i], $responseViewData[$i][$orderBy]);
        }
    }

    /** @test */
    public function user_can_create_a_skill()
    {
        $this->userSigningIn();
        $this->assertEquals(0, (Skill::all())->count());

        $skill = $this->getDataFromFactory(Skill::class);
        $response = $this->post('admin/skills', $skill);
        $createdSkill = Skill::first();

        $this->assertEquals(1, (Skill::all())->count());
        $this->assertEquals($skill['title'], $createdSkill->title);
        $this->assertEquals($skill['description'], $createdSkill->description);
        $this->assertEquals($skill['level'], $createdSkill->level);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHasAll(['messages', 'status']);
    }

    /** @test */
    public function user_should_redirect_back_when_trying_to_store_invalid_skill_request()
    {
        $this->userSigningIn();

        $response = $this->post('admin/skills', [
            'title' => null,
            'level' => null
        ]);

        $oldInput = $response->getSession()->getOldInput();

        $this->assertEquals(0, (Skill::all())->count());
        $this->assertArrayHasKey('title', $oldInput);
        $this->assertArrayHasKey('level', $oldInput);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['title', 'level']);
    }

    /** @test */
    public function user_can_update_a_skill()
    {
        $this->userSigningIn();

        $skillData = $this->getDataFromFactory(Skill::class);
        $skill = Skill::create($skillData);

        $newSkillData = $this->getDataFromFactory(Skill::class);
        $response = $this->put("admin/skills/{$skill->id}", $newSkillData);

        $updatedSkill = Skill::first();

        $this->assertEquals(1, (Skill::all())->count());
        $this->assertEquals($newSkillData['title'], $updatedSkill->title);
        $this->assertEquals($newSkillData['description'], $updatedSkill->description);
        $this->assertEquals($newSkillData['level'], $updatedSkill->level);

        $response->assertRedirect();
        $response->assertSessionHasAll(['messages', 'status']);
        $response->assertSessionHasNoErrors();

    }

    /** @test */
    public function user_can_see_error_messages_on_failure_update()
    {
        $this->userSigningIn();

        $skill = Skill::factory()->create();

        $response = $this->put("admin/skills/{$skill->id}", [
            'title' => null,
            'level' => null
        ]);

        $oldInput = $response->getSession()->getOldInput();

        $skillOnDatabase = Skill::find($skill->id);

        $this->assertEquals(1, (Skill::all())->count());
        $this->assertEquals($skill->title, $skillOnDatabase->title);
        $this->assertEquals($skill->description, $skillOnDatabase->description);
        $this->assertEquals($skill->level, $skillOnDatabase->level);

        $this->assertArrayHasKey('title', $oldInput);
        $this->assertArrayHasKey('level', $oldInput);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['title', 'level']);
    }
    

    /** @test */
    public function user_can_delete_a_skill()
    {
        $this->userSigningIn();

        $skill = Skill::factory()->create();
        $this->assertEquals(1, (Skill::all())->count());

        $response = $this->delete("admin/skills/{$skill->id}");

        $this->assertEquals(0, (Skill::all())->count());
        $response->assertRedirect();
        $response->assertSessionHasAll(['messages', 'status']);
        $response->assertSessionHasNoErrors();
    }
}
