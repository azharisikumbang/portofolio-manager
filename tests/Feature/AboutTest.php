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

    private function _get_sample_data()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'photo' => UploadedFile::fake()->image('image.jpg'),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'description' => $this->faker->sentence(),
            'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf')
        ];
    }

    private function _get_schema_data()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'photo' => (UploadedFile::fake()->image('image.jpg'))->getClientOriginalName(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'description' => $this->faker->sentence(),
            'cv' => (UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf'))->getClientOriginalName()
        ];
    }

    public function test_about_screen_can_be_rendered()
    {
        $user = User::factory()->create();

        $image = UploadedFile::fake()->image('image.jpg');
        $cv = UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf');

        About::create($this->_get_sample_data());

        $response = $this->actingAs($user)
                        ->get('admin/me/');

        $response->assertStatus(200);
    }

    public function test_edit_screen_can_be_rendered()
    {
        $user = User::factory()->create();

        About::create($this->_get_schema_data());

        $aboutData = About::first();

        $response = $this->actingAs($user)
                        ->get('admin/me/edit/');

        $response->assertStatus(200);
    }

    public function test_users_should_be_redirect_to_create_screen_if_no_rows()
    {
        $user = User::factory()->create();

        // index page
        $response = $this->actingAs($user)
                        ->get('admin/me');

        $response->assertRedirect('admin/me/create');

        // edit page
        $response = $this->actingAs($user)
                        ->get('admin/me/edit');

        $response->assertRedirect('admin/me/create');


        // update
        $response = $this->actingAs($user)
                        ->get('admin/me/edit', $this->_get_sample_data());

        $response->assertRedirect('admin/me/create');

    }

    public function test_users_can_store_valid_values()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $image = UploadedFile::fake()->image('image.jpg');
        $cv = UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf');

        $name = $this->faker->name();
        $email = $this->faker->unique()->safeEmail();
        $phone = $this->faker->phoneNumber();
        $address = $this->faker->address();
        $description = $this->faker->sentence();


        $data = [
            'name' => $name,
            'email' => $email,
            'photo' => $image,
            'phone' => $phone,
            'address' => $address,
            'description' => $description,
            'cv' => $cv
        ];

        $response = $this->actingAs($user)
                        ->post('admin/me', $data);

        $response->assertRedirect('admin/me');
        $response->assertSessionHasNoErrors();

        $aboutData = About::first();

        $this->assertNotNull($aboutData);

        $this->assertEquals($name, $aboutData->name);
        $this->assertEquals($email, $aboutData->email);
        $this->assertEquals($phone, $aboutData->phone);
        $this->assertEquals($address, $aboutData->address);
        $this->assertEquals($description, $aboutData->description);
        
    }

    public function test_users_only_can_store_a_row()
    {
        About::create($this->_get_schema_data());

        $user = User::factory()->create();
        $response = $this->actingAs($user)
                        ->post('admin/me', $this->_get_sample_data());

        $response->assertRedirect('admin/me');
        $response->assertSessionHasErrors();
    }

    public function test_users_cannot_store_empty_data_and_should_return_error_messages()
    {
        $user = User::factory()->create();
        $data = [];

        $response = $this->actingAs($user)
                        ->post('admin/me', $data);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_users_can_not_store_without_required_data_and_should_return_error_messages()
    {
        $user = User::factory()->create();
        $data = [
            'name' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('admin/me', $data);

        $response->assertSessionHasErrors(['name']);

    }

    public function test_users_can_update_the_data()
    {
        $this->withoutExceptionHandling();

        About::create($this->_get_schema_data());

        $name = $this->faker->name();
        $email = $this->faker->unique()->safeEmail();
        $phone = $this->faker->phoneNumber();
        $address = $this->faker->address();
        $description = $this->faker->sentence();

        $newImage = UploadedFile::fake()->image('new_image.jpg');
        $newCv = UploadedFile::fake()->create('new_cv.pdf', 1024, 'application/pdf');

        $replaceData = [
            'name' => $name,
            'email' => $email,
            'photo' => $newImage,
            'phone' => $phone,
            'address' => $address,
            'description' => $description,
            'cv' => $newCv,
        ];

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->put('admin/me', $replaceData);

        $response->assertRedirect('admin/me');
        $response->assertSessionHasNoErrors();

        $updatedAboutData = About::first();

        $this->assertNotNull($updatedAboutData);

        $this->assertEquals($name, $updatedAboutData->name);
        $this->assertEquals($email, $updatedAboutData->email);
        $this->assertEquals($phone, $updatedAboutData->phone);
        $this->assertEquals($address, $updatedAboutData->address);
        $this->assertEquals($description, $updatedAboutData->description);
    }

    public function test_users_can_not_update_with_invalid_data_and_should_return_error_messages()
    {
        About::create($this->_get_schema_data());

        $invalidRequestData = [];
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->put('admin/me', $invalidRequestData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_users_not_allowed_to_delete_the_data()
    {
        $response = $this->delete('admin/me');
        $response->assertStatus(405);
    }

    public function test_authenticated_users_not_allowed_to_delete_the_data()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('admin/me');
        $response->assertStatus(405);
    }
}
