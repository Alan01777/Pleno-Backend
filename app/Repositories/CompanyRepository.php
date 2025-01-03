<?php

namespace App\Repositories;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Models\Company;

/**
 * Class CompanyRepository
 *
 * This class implements the CompanyRepositoryInterface.
 *
 * @package App\Repositories
 */

class CompanyRepository implements CompanyRepositoryInterface
{

    /**
     * Get all companies.
     * @return array
     */
    public function findAll(): array
    {
        return Company::where('id', '>', 0)->get()->toArray();
    }

    /**
     * Create a new company.
     * @param array $data The company data.
     * @return Company The created company.
     */
    public function create(array $data): company
    {
        return Company::create($data);
    }

    /**
     * Find a company by its ID.
     * @param int $id The ID of the company.
     * @return Company|null The company if found, null otherwise.
     */
    public function findById(int $id): ?Company
    {
        return Company::where('id', $id)->first();
    }

    /**
     * Update a company by its ID.
     * @param int $id The ID of the company.
     * @param array $data The company data.
     * @return bool True if the company was updated, false otherwise.
     */
    public function update(int $id, array $data): bool
    {
        $company = Company::where('id', $id)->first();
        if ($company) {
            return $company->update($data);
        }
        return false;
    }

    /**
     * Delete a company by its ID.
     * @param int $id The ID of the company.
     * @return bool True if the company was deleted, false otherwise.
     */
    public function delete(int $id): bool
    {
        $company = Company::where('id', $id)->first();
        if ($company) {
            return $company->delete();
        }
        return false;
    }

    /**
     * Find a company by its email.
     * @param string $email The email of the company.
     * @return Company|null The company if found, null otherwise.
     */
    public function findByEmail(string $email): ?Company
    {
        return Company::where('email', $email)->first();
    }

    /**
     * Find a company by its CNPJ.
     * @param string $cnpj The CNPJ of the company.
     * @return Company|null The company if found, null otherwise.
     */
    public function findByCnpj(string $cnpj): ?Company
    {
        return Company::where('cnpj', $cnpj)->first();
    }

    /**
     * Find a company by its trade name.
     * @param string $tradeName The trade name of the company.
     * @return Company|null The company if found, null otherwise.
     */
    public function findByTradeName(string $tradeName): Company|null
    {
        return Company::where('trade_name', $tradeName)->first();
    }

    /**
     * Find a company by its legal name.
     * @param string $legalName The legal name of the company.
     * @return Company|null The company if found, null otherwise.
     */
    public function findByLegalName(string $legalName): Company|null
    {
        return Company::where('legal_name', $legalName)->first();
    }

    /**
     * Find a company by its phone.
     * @param string $phone The phone of the company.
     * @return Company|null The company if found, null otherwise.
     */
    public function findByPhone(string $phone): Company|null
    {
        return Company::where('phone', $phone)->first();
    }

    /**
     * Find a company by its size.
     * @param string $size The size of the company.
     * @return array The company if found, null otherwise.
     */
    public function findBySize(string $size): array
    {
        return Company::where('size', $size)->get()->toArray();
    }

    public function findAllByUserId(int $id): array
    {
        // return all companies that have the user ID
        return Company::where('user_id', $id)->get()->toArray();
    }
}
