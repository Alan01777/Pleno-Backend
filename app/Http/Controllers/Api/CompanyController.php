<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Contracts\Services\CompanyServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

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
        try {
            $data = $request->validated();
            $company = $this->companyService->create($data);
            return response()->json($company, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show a company by its ID.
     *
     * @param int $id The ID of the company.
     * @return JsonResponse The response containing the company data.
     */
    public function show($id): JsonResponse
    {
        try {
            $company = $this->companyService->findById($id);
            if (!$company) {
                throw new NotFoundHttpException('Company not found.');
            }
            return response()->json($company);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
        try {
            $data = $request->validated();
            $company = $this->companyService->update($id, $data);
            return response()->json($company);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a company by its ID.
     *
     * @param int $id The ID of the company.
     * @return JsonResponse The response containing the deleted company data.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->companyService->delete($id);
            return response()->json(null, 204);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * List all companies.
     *
     * @return JsonResponse The response containing the list of companies.
     */
    public function index(): JsonResponse
    {
        try {
            $companies = $this->companyService->findAll();
            return response()->json($companies);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
