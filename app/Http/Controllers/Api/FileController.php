<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\FileServiceInterface;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Store newly created files in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Adjust the validation rules as needed
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $files = $request->file('file');
        $companyId = $request->input('company_id');
        $createdFiles = $this->fileService->create($files, $companyId);

        return response()->json($createdFiles, 201);
    }

    /**
     * Update the specified file in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Adjust the validation rules as needed
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $file = $request->file('file');
        $companyId = $request->input('company_id');
        $updated = $this->fileService->update($id, $file, $companyId);

        return response()->json(['updated' => $updated]);
    }

    /**
     * Remove the specified file from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $deleted = $this->fileService->delete($id);

        return response()->json(['deleted' => $deleted]);
    }

    public function index()
    {
        $files = $this->fileService->findAllByUserId();

        return response()->json([$files]);
    }
}
