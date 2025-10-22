<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploader
{
    protected string $disk = 'public';
    protected string $rootFolder = 'uploads';

    /**
     * set disk here like s3 or public
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * main upload method : handle both uploaded file and base64 string
     * * @param mixed $fileData UploadedFile instance, or Base64 string
     * @param string $folder name of the sub-folder (like 'users' or 'products')
     * @return string|null the actual path of the saved file
     */
    public function upload($fileData, string $folder): ?string
    {
        if ($fileData instanceof UploadedFile) {
            // if the input is an uploaded file [generally from a form]
            return $this->uploadFromFile($fileData, $folder);
        } elseif (is_string($fileData) && Str::startsWith($fileData, 'data:')) {
            // if the input is a Base64 string
            return $this->uploadFromBase64($fileData, $folder);
        }
        // otherwise
        return null;
    }



    /**
     * private method: UploadedFile save instance।
     */
    private function uploadFromFile(UploadedFile $file, string $folder): ?string
    {
        $originalExtension = $file->getClientOriginalExtension();
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $baseName . '-' . time() . '.' . $originalExtension;

        $path = $this->rootFolder . '/' . trim($folder, '/');

        try {
            $filePath = Storage::disk($this->disk)->putFileAs($path, $file, $fileName);
            return $filePath;
        } catch (\Exception $e) {
            \Log::error("File Upload Failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * private method: it's stores file from Base64 ।
     */
    private function uploadFromBase64(string $base64Data, string $folder): ?string
    {
        // getting file type from Base64 string
        if (!preg_match('/^data:(\w+)\/(\w+);base64,/', $base64Data, $type)) {
            return null;
        }

        $fileData = substr($base64Data, strpos($base64Data, ',') + 1);
        $fileType = $type[2] ?? 'png';
        $fileBinary = base64_decode($fileData);

        $fileName = Str::random(40) . '-' . time() . '.' . $fileType;
        $path = $this->rootFolder . '/' . trim($folder, '/') . '/' . $fileName;

        try {
            // directly save binary data in disk
            Storage::disk($this->disk)->put($path, $fileBinary);
            return $path;
        } catch (\Exception $e) {
            \Log::error("Base64 Upload Failed: " . $e->getMessage());
            return null;
        }
    }


    /**
     * file delete
     */
    public function delete(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($filePath)) {
            return Storage::disk($this->disk)->delete($filePath);
        }

        return false;
    }
}
