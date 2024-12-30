<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Contracts\Services\CompanyServiceInterface;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyServiceInterface $companyService)
    {
        $this->companyService = $companyService;
    }

    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        $company = $this->companyService->create($data);

        return response()->json($company, 201);
    }

    public function show($id)
    {
        $company = $this->companyService->findById($id);

        return response()->json($company);
    }

    public function update(CompanyRequest $request, $id)
    {
        $data = $request->validated();
        $company = $this->companyService->update($id, $data);

        return response()->json($company);
    }

    public function destroy($id)
    {
        $this->companyService->delete($id);

        return response()->json(null, 204);
    }

    public function index()
    {
        $companies = $this->companyService->findAll();

        return response()->json($companies);
    }
}
