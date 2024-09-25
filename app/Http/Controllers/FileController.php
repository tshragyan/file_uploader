<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Services\FileService;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function upload(UploadFileRequest $request, FileService $service)
    {
        $response = $service->uploadFile(array_merge($request->validated(), ['file' => $request->file('file')]));
        if ($response['status']) {
           return response()->json(['status' =>  'success']);
        }

        Log::error($response['message']);
        return response()->json(['status' => 'failed'], 400);
    }

}
