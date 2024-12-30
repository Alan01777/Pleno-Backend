<?php

namespace App\Contracts\Repositories;

use App\Models\Company;
/**
 * Interface CompanyRepositoryInterface
 *
 * This interface defines the contract for a repository that handles operations related to the Company model.
 *
 * @package App\Contracts\Repositories
 */

interface CompanyRepositoryInterface
{
    /**
     * Find all companies.
     * @return array The array of company instances.
     */

    public function findAll(): array;

    /**
     * Create a new company.
     *
     * @param array $data The data to create the company with.
     * @return Company The created company instance.
     */
    public function create(array $data): Company;

    /**
     * Find a company by its ID.
     *
     * @param int $id The ID of the company.
     * @return Company|null The company instance or null if not found.
     */
    public function findById(int $id): ?Company;

    /**
     * Update a company by its ID.
     *
     * @param int $id The ID of the company.
     * @param array $data The data to update the company with.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a company by its ID.
     *
     * @param int $id The ID of the company.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete(int $id): bool;

    /**
     * Find a company by its email.
     *
     * @param string $email The email of the company.
     * @return Company|null The company instance or null if not found.
     */
    public function findByEmail(string $email): ?Company;

    /**
     * Find a company by its CNPJ.
     *
     * @param string $cnpj The CNPJ of the company.
     * @return Company|null The company instance or null if not found.
     */
    public function findByCnpj(string $cnpj): ?Company;

    /**
     * Find a company by its trade name.
     *
     * @param string $tradeName The trade name of the company.
     * @return Company|null The company instance or null if not found.
     */
    public function findByTradeName(string $tradeName): ?Company;

    /**
     * Find a company by its legal name.
     *
     * @param string $legalName The legal name of the company.
     * @return Company|null The company instance or null if not found.
     */
    public function findByLegalName(string $legalName): ?Company;

    /**
     * Find a company by its phone number.
     *
     * @param string $phone The phone number of the company.
     * @return Company|null The company instance or null if not found.
     */
    public function findByPhone(string $phone): ?Company;

    /**
     * Find multiple companies by size.
     *
     * @param string $size The size of the companies.
     * @return array The array of company instances.
     */
    public function findBySize(string $size): array;
}
