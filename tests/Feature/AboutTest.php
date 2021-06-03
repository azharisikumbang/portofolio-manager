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
                        ->get('admin/about/');

        $response->assertStatus(200);
    }

    public function test_edit_screen_can_be_rendered()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        About::create($this->_get_schema_data());

        $aboutData = About::first();

        $response = $this->actingAs($user)
                        ->get('admin/about/edit/');

        $response->assertStatus(200);
    }

    public function test_redirect_to_create_screen_if_there_is_no_about_data()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        // index page
        $response = $this->actingAs($user)
                        ->get('admin/about');

        $response->assertRedirect('admin/about/create');

        // edit page
        $response = $this->actingAs($user)
                        ->get('admin/about/edit');

        $response->assertRedirect('admin/about/create');


        // update
        $response = $this->actingAs($user)
                        ->get('admin/about/edit', $this->_get_sample_data());

        $response->assertRedirect('admin/about/create');

    }

    public function test_store_data_with_valid_values_was_stored_successfully()
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
                        ->post('admin/about/store', $data);

        $response->assertRedirect('admin/about');
        $response->assertSessionHasNoErrors();

        $aboutData = About::first();

        $this->assertNotNull($aboutData);

        $this->assertEquals($name, $aboutData->name);
        $this->assertEquals($email, $aboutData->email);
        $this->assertEquals($phone, $aboutData->phone);
        $this->assertEquals($address, $aboutData->address);
        $this->assertEquals($description, $aboutData->description);
        
    }

    public function test_data_cannot_more_than_one_row()
    {
        $this->withoutExceptionHandling();

        About::create($this->_get_schema_data());

        $user = User::factory()->create();
        $response = $this->actingAs($user)
                        ->post('admin/about/store', $this->_get_sample_data());

        $response->assertRedirect('admin/about');
        $response->assertSessionHasErrors();
    }

    public function test_store_with_empty_request_has_error_messages()
    {
        $user = User::factory()->create();
        $data = [];

        $response = $this->actingAs($user)
                        ->post('admin/about/store', $data);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_store_with_null_request_values_has_error_messages()
    {
        $user = User::factory()->create();
        $data = [
            'name' => null,
            'email' => null,
            'photo' => null,
            'phone' => null,
            'address' => null,
            'description' => null,
            'cv' => null
        ];

        $response = $this->actingAs($user)
                        ->post('admin/about/store', $data);

        $response->assertSessionHasErrors(['name']);

    }

    public function test_update_data_with_valid_values_was_updated_succesfully()
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
                        ->put('admin/about/update', $replaceData);

        $response->assertRedirect('admin/about');
        $response->assertSessionHasNoErrors();

        $updatedAboutData = About::first();

        $this->assertNotNull($updatedAboutData);

        $this->assertEquals($name, $updatedAboutData->name);
        $this->assertEquals($email, $updatedAboutData->email);
        $this->assertEquals($phone, $updatedAboutData->phone);
        $this->assertEquals($address, $updatedAboutData->address);
        $this->assertEquals($description, $updatedAboutData->description);
    }

    public function test_update_with_invalid_request_values_has_errors_message()
    {
        About::create($this->_get_schema_data());

        $invalidRequestData = [];
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->put('admin/about/update', $invalidRequestData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_trying_to_delete_data_without_user_should_return_method_not_allowed()
    {
        $response = $this->delete('admin/about');
        $response->assertStatus(405);
    }

    public function test_trying_to_delete_data_with_user_should_return_method_not_allowed()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('admin/about');
        $response->assertStatus(405);
    }
}
