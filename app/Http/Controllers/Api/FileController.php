<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\FileServiceInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index()
    {
        try {
            $files = $this->fileService->findAllByUserId();
            return response()->json($files);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        try {
            $file = $request->file('file');
            $companyId = $request->input('company_id');
            $createdFile = $this->fileService->create($file, $companyId);
            return response()->json($createdFile, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $file = $this->fileService->findById($id);
            return response()->json($file);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        try {
            $file = $request->file('file');
            $companyId = $request->input('company_id');
            $updated = $this->fileService->update($id, $file, $companyId);
            return response()->json(['updated' => $updated]);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->fileService->delete($id);
            return response()->json(['deleted' => $deleted]);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
