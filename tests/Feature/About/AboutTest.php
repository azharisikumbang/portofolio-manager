<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\About;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AboutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_about_screen_can_be_rendered()
    {
        $this->userSigningIn();

        About::create($this->getDataFromFactory(About::class));

        $response = $this->get('admin/me/');

        $response->assertStatus(200);
    }

    public function test_edit_screen_can_be_rendered()
    {
        $this->userSigningIn();

        About::create($this->getDataFromFactory(About::class));

        $aboutData = About::first();

        $response = $this->get('admin/me/edit/');

        $response->assertStatus(200);
    }

    public function test_users_should_be_redirect_to_create_screen_if_there_is_no_existing_row()
    {
        $this->withoutExceptionHandling();
        $this->userSigningIn();

        // index page
        $response = $this->get('admin/me');

        $response->assertRedirect('admin/me/create');

        // edit page
        $response = $this->get('admin/me/edit');

        $response->assertRedirect('admin/me/create');

        // update
        $data = $this->getDataFromFactory(About::class, [
            'photo' => UploadedFile::fake()->image('image.jpg'),
            'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf')
        ]);

        $response = $this->put('admin/me', $data);

        $response->assertRedirect('admin/me/create');

    }

    public function test_users_can_store_valid_values()
    {
        $this->userSigningIn();

        $requestData = $this->getDataFromFactory(About::class, [
            'photo' => UploadedFile::fake()->image('image.jpg'),
            'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf')
        ]);

        $response = $this->post('admin/me', $requestData);

        $response->assertRedirect('admin/me');
        $response->assertSessionHasNoErrors();

        $recordOnDatabase = About::first();

        $this->assertNotNull($recordOnDatabase);

        $this->assertEquals($requestData['name'], $recordOnDatabase->name);
        $this->assertEquals($requestData['email'], $recordOnDatabase->email);
        $this->assertEquals($requestData['phone'], $recordOnDatabase->phone);
        $this->assertEquals($requestData['address'], $recordOnDatabase->address);
        $this->assertEquals($requestData['description'], $recordOnDatabase->description);
        
    }

    public function test_users_only_can_store_a_row()
    {
        $this->userSigningIn();
        About::create($this->getDataFromFactory(About::class));
        
        $response = $this->post('admin/me', $this->getDataFromFactory(About::class, [
            'photo' => UploadedFile::fake()->image('image.jpg'),
            'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf')
        ]));

        $response->assertRedirect('admin/me');
        $response->assertSessionHasErrors();
    }

    public function test_users_cannot_store_empty_data_and_should_return_error_messages()
    {
        $this->userSigningIn();
        $data = [];

        $response = $this->post('admin/me', $data);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_users_can_not_store_without_required_data_and_should_return_error_messages()
    {
        $this->userSigningIn();
        $data = [
            'name' => null,
        ];

        $response = $this->post('admin/me', $data);

        $response->assertSessionHasErrors(['name']);

    }

    public function test_users_can_update_the_data()
    {
        $this->userSigningIn();

        About::create($this->getDataFromFactory(About::class));

        $editRequestData = $this->getDataFromFactory(About::class, [
            'photo' => UploadedFile::fake()->image('image.jpg'),
            'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf')
        ]);

        $response = $this->put('admin/me', $editRequestData);

        $response->assertRedirect('admin/me');
        $response->assertSessionHasNoErrors();

        $updatedAboutData = About::first();

        $this->assertNotNull($updatedAboutData);

        $this->assertEquals($editRequestData['name'], $updatedAboutData->name);
        $this->assertEquals($editRequestData['email'], $updatedAboutData->email);
        $this->assertEquals($editRequestData['phone'], $updatedAboutData->phone);
        $this->assertEquals($editRequestData['address'], $updatedAboutData->address);
        $this->assertEquals($editRequestData['description'], $updatedAboutData->description);
    }

    public function test_users_can_not_update_with_invalid_data_and_should_return_error_messages()
    {
        $this->userSigningIn();
        About::create($this->getDataFromFactory(About::class));

        $invalidRequestData = [];
        $response = $this->put('admin/me', $invalidRequestData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_users_not_allowed_to_delete_the_data()
    {
        $response = $this->delete('admin/me');
        $response->assertStatus(405);
    }

    public function test_authenticated_users_not_allowed_to_delete_the_data()
    {
        $this->userSigningIn();
        $response = $this->delete('admin/me');
        $response->assertStatus(405);
    }
}
