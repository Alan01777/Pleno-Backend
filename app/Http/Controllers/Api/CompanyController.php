<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Contracts\Services\CompanyServiceInterface;
use Illuminate\Http\JsonResponse;

/**
 * Class CompanyController
 *
 * This controller handles the company actions such as create, update, delete and list.
 *
 * @package App\Http\Controllers\Api
 */
class CompanyController extends Controller
{
    protected $companyService;

    /**
     * CompanyController constructor.
     *
     * @param CompanyServiceInterface $companyService The company service interface.
     */
    public function __construct(CompanyServiceInterface $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Store a new company.
     *
     * @param CompanyRequest $request The request containing the company data.
     * @return JsonResponse The response containing the created company data.
     */
    public function store(CompanyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $company = $this->companyService->create($data);

        return response()->json($company, 201);
    }

    /**
     * Show a company by its ID.
     *
     * @param int $id The ID of the company.
     * @return JsonResponse The response containing the company data.
     */
    public function show($id): JsonResponse
    {
        $company = $this->companyService->findById($id);

        return response()->json($company);
    }

    /**
     * Update a company by its ID.
     *
     * @param CompanyRequest $request The request containing the company data.
     * @param int $id The ID of the company.
     * @return JsonResponse The response containing the updated company data.
     */
    public function update(CompanyRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $company = $this->companyService->update($id, $data);

        return response()->json($company);
    }

    /**
     * Delete a company by its ID.
     *
     * @param int $id The ID of the company.
     * @return JsonResponse The response containing the deleted company data.
     */
    public function destroy($id): JsonResponse
    {
        $this->companyService->delete($id);

        return response()->json(null, 204);
    }

    /**
     * List all companies.
     *
     * @return JsonResponse The response containing the list of companies.
     */
    public function index(): JsonResponse
    {
        $companies = $this->companyService->findAll();

        return response()->json($companies);
    }
}
