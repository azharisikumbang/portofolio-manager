<?php

namespace Tests\Unit\Utils;

use App\Utils\FileUploader;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploaderTest extends TestCase
{
    public function test_photo_can_be_uploaded()
    {   
        $this->withoutExceptionHandling();

        $imagesStorageLocation = "/images";
    	$file = UploadedFile::fake()->image('image.jpg');

    	$uplaodedFile = FileUploader::uploadAsPhoto($file);

        Storage::disk('local')->assertExists(
            FileUploader::joinPaths(
                FileUploader::LOCATION,
                $imagesStorageLocation,
                $uplaodedFile->getClientOriginalName()
            )
        );
    }

    public function test_get_valid_image_file_attribute() 
    {
    	$filename = "/path/to/files/img_sample.jpeg";

    	$fileAttributes = FileUploader::getFileAttributes($filename);

    	$this->assertIsArray($fileAttributes);
    	$this->assertArrayHasKey('name', $fileAttributes);
    	$this->assertArrayHasKey('ext', $fileAttributes);
    	$this->assertArrayHasKey('path', $fileAttributes);

    	$this->assertEquals('img_sample.jpeg', $fileAttributes['name']);
    	$this->assertEquals('jpeg', $fileAttributes['ext']);
    	$this->assertEquals('public/assets/images/', $fileAttributes['path']);
    }


    public function test_get_valid_doc_file_attribute() 
    {
        $filename = "/path/to/files/docs_sample.pdf";

        $fileAttributes = FileUploader::getFileAttributes($filename);

        $this->assertIsArray($fileAttributes);
        $this->assertArrayHasKey('name', $fileAttributes);
        $this->assertArrayHasKey('ext', $fileAttributes);
        $this->assertArrayHasKey('path', $fileAttributes);

        $this->assertEquals('docs_sample.pdf', $fileAttributes['name']);
        $this->assertEquals('pdf', $fileAttributes['ext']);
        $this->assertEquals('public/assets/files/', $fileAttributes['path']);
    }

    public function test_join_some_path()
    {
        $path = "/some/path/to/";
        $anotherPath = "/to/file/sample.jpeg";

        $joinedPath = FileUploader::joinPaths($path, $anotherPath);

        $this->assertEquals("/some/path/to/to/file/sample.jpeg", $joinedPath);
    }
}
