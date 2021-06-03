<?php 

namespace App\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class FileUploader 
{
	public const LOCATION = 'public/assets/';

	public static function upload(UploadedFile $file, string $path, string $name) : File
	{
		$isUploaded = $file->storeAs($path, $name);

		if (!$isUploaded) {
			throw new UploadException(
				sprintf("Failed to upload '%s' to %s", $file->getClientOriginalName(), $path)
			);
		}

		$fullStorageFolder = (Storage::disk('local'))
								->getDriver()
								->getAdapter()
								->getPathPrefix();

		return new UploadedFile(
			self::joinPaths($fullStorageFolder, $path, $name), 
			$name
		);
	}

	public static function joinPaths() : string
	{
	    $paths = [];

	    foreach (func_get_args() as $arg) {
	        if ($arg !== '') { $paths[] = $arg; }
	    }

	    return preg_replace('#/+#','/', join('/', $paths));
	}

	public static function uploadAsPhoto(File $file) : File 
	{
		$path = self::LOCATION . "images/";
		$filename = 'img_' . time() . '.' . $file->getClientOriginalExtension();

		return self::upload($file, $path, $filename);
	}

	public static function uploadAsDocument(File $file) : File 
	{
		$path = self::LOCATION . "files/";
		$filename = 'doc_' . $file->getClientOriginalName();

		return self::upload($file, $path, $filename);
	}

	public static function moveToArchive(string $file) : boolean
	{
		// todo
		$file = new UploadedFile($file, self::getPrefix($file));

		return true;
	}

	public static function getFileAttributes(string $file) : array
	{
		$nameArray = explode('/', $file);
		$name = $nameArray[count($nameArray) - 1];

		$extArray = explode('.', $file);
		$ext = $extArray[count($extArray) - 1];

		$prefix = explode('_', $name); 
		$location = (strtolower($prefix[0]) == 'img') ? 'images/' : 'files/';
	
		return [
			'name' => $name,
			'ext' => $ext,
			'path' => self::LOCATION . $location
		];
	}

}