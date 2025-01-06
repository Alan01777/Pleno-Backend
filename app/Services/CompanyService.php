<?php

namespace App\Services;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class CompanyService
 *
 * This class implements the CompanyServiceInterface.
 *
 * @package App\Services
 */
class CompanyService implements CompanyServiceInterface
{
    private CompanyRepositoryInterface $companyRepository;

    /**
     * CompanyService constructor.
     *
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Get all companies.
     * @return array
     */
    public function findAll(): array
    {
        try {
            return $this->companyRepository->findAll();
        } catch (Exception $e) {
            Log::error('Failed to retrieve companies: ' . $e->getMessage());
            throw new Exception('Failed to retrieve companies.');
        }
    }

    /**
     * Create a new company.
     * @param array $data The company data.
     * @return array The created company.
     */
    public function create(array $data): array
    {
        try {
            return $this->companyRepository->create($data)->toArray();
        } catch (Exception $e) {
            Log::error('Failed to create company: ' . $e->getMessage());
            throw new Exception('Failed to create company.');
        }
    }

    /**
     * Find a company by its ID.
     * @param int $id The ID of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findById(int $id): array|null
    {
        try {
            $company = $this->companyRepository->findById($id);
            return $company ? $company->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find company: ' . $e->getMessage());
            throw new Exception('Failed to find company.');
        }
    }

    /**
     * Update a company by its ID.
     * @param int $id The ID of the company.
     * @param array $data The company data.
     * @return array The updated company.
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->companyRepository->update($id, $data);
            return $this->companyRepository->findById($id)->toArray();
        } catch (Exception $e) {
            Log::error('Failed to update company: ' . $e->getMessage());
            throw new Exception('Failed to update company.');
        }
    }

    /**
     * Delete a company by its ID.
     * @param int $id The ID of the company.
     * @return bool True if the company was deleted, false otherwise.
     */
    public function delete(int $id): bool
    {
        try {
            return $this->companyRepository->delete($id);
        } catch (Exception $e) {
            Log::error('Failed to delete company: ' . $e->getMessage());
            throw new Exception('Failed to delete company.');
        }
    }

    /**
     * Find a company by its email.
     * @param string $email The email of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByEmail(string $email): array|null
    {
        try {
            $company = $this->companyRepository->findByEmail($email);
            return $company ? $company->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find company by email: ' . $e->getMessage());
            throw new Exception('Failed to find company by email.');
        }
    }

    /**
     * Find a company by its CNPJ.
     * @param string $cnpj The CNPJ of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByCnpj(string $cnpj): array|null
    {
        try {
            $company = $this->companyRepository->findByCnpj($cnpj);
            return $company ? $company->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find company by CNPJ: ' . $e->getMessage());
            throw new Exception('Failed to find company by CNPJ.');
        }
    }

    /**
     * Find a company by its trade name.
     * @param string $tradeName The trade name of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByTradeName(string $tradeName): array|null
    {
        try {
            $company = $this->companyRepository->findByTradeName($tradeName);
            return $company ? $company->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find company by trade name: ' . $e->getMessage());
            throw new Exception('Failed to find company by trade name.');
        }
    }

    /**
     * Find a company by its legal name.
     * @param string $legalName The legal name of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByLegalName(string $legalName): array|null
    {
        try {
            $company = $this->companyRepository->findByLegalName($legalName);
            return $company ? $company->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find company by legal name: ' . $e->getMessage());
            throw new Exception('Failed to find company by legal name.');
        }
    }

    /**
     * Find a company by its phone.
     * @param string $phone The phone of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByPhone(string $phone): array|null
    {
        try {
            $company = $this->companyRepository->findByPhone($phone);
            return $company ? $company->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find company by phone: ' . $e->getMessage());
            throw new Exception('Failed to find company by phone.');
        }
    }

    /**
     * Find a company by its size.
     * @param string $size The size of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findBySize(string $size): array|null
    {
        try {
            $company = $this->companyRepository->findBySize($size);
            return $company ? $company : null;
        } catch (Exception $e) {
            Log::error('Failed to find company by size: ' . $e->getMessage());
            throw new Exception('Failed to find company by size.');
        }
    }

    public function findAllByUserId(): array
    {
        try {
            $id = Auth::id();
            return $this->companyRepository->findAllByUserId($id);
        } catch (Exception $e) {
            Log::error('Failed to find companies by user ID: ' . $e->getMessage());
            throw new Exception('Failed to find companies by user ID.');
        }
    }
}
