<?php

namespace App\Services;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;
use Illuminate\Support\Facades\Auth;

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
        return $this->companyRepository->findAll();
    }

    /**
     * Create a new company.
     * @param array $data The company data.
     * @return array The created company.
     */
    public function create(array $data): array
    {
        return $this->companyRepository->create($data)->toArray();
    }

    /**
     * Find a company by its ID.
     * @param int $id The ID of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findById(int $id): array|null
    {
        $company = $this->companyRepository->findById($id);
        return $company ? $company->toArray() : null;
    }

    /**
     * Update a company by its ID.
     * @param int $id The ID of the company.
     * @param array $data The company data.
     * @return array The updated company.
     */
    public function update(int $id, array $data): array
    {
        $this->companyRepository->update($id, $data);
        return $this->companyRepository->findById($id)->toArray();
    }

    /**
     * Delete a company by its ID.
     * @param int $id The ID of the company.
     * @return bool True if the company was deleted, false otherwise.
     */
    public function delete(int $id): bool
    {
        return $this->companyRepository->delete($id);
    }

    /**
     * Find a company by its email.
     * @param string $email The email of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByEmail(string $email): array|null
    {
        $company = $this->companyRepository->findByEmail($email);
        return $company ? $company->toArray() : null;
    }

    /**
     * Find a company by its CNPJ.
     * @param string $cnpj The CNPJ of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByCnpj(string $cnpj): array|null
    {
        $company = $this->companyRepository->findByCnpj($cnpj);
        return $company ? $company->toArray() : null;
    }

    /**
     * Find a company by its trade name.
     * @param string $tradeName The trade name of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByTradeName(string $tradeName): array|null
    {
        $company = $this->companyRepository->findByTradeName($tradeName);
        return $company ? $company->toArray() : null;
    }

    /**
     * Find a company by its legal name.
     * @param string $legalName The legal name of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByLegalName(string $legalName): array|null
    {
        $company = $this->companyRepository->findByLegalName($legalName);
        return $company ? $company->toArray() : null;
    }

    /**
     * Find a company by its phone.
     * @param string $phone The phone of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findByPhone(string $phone): array|null
    {
        $company = $this->companyRepository->findByPhone($phone);
        return $company ? $company->toArray() : null;
    }

    /**
     * Find a company by its size.
     * @param string $size The size of the company.
     * @return array|null The company if found, null otherwise.
     */
    public function findBySize(string $size): array|null
    {
        $company = $this->companyRepository->findBySize($size);
        return $company ? $company : null;
    }

    public function findAllByUserId(): array
    {
        $id = Auth::id();
        return $this->companyRepository->findAllByUserId($id);
    }
}
