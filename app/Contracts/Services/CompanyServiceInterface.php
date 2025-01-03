<?php

namespace App\Contracts\Services;

/**
 * Interface AuthServiceInterface
 *
 * This interface defines the methods for authentication services.
 *
 * @package App\Contracts\Services
 */

interface CompanyServiceInterface
{
    /**
     * Create a new company.
     *
     * @param array $data
     * @return array
     */
    public function create(array $data): array;

    /**
     * Find all companies.
     *
     * @return array
     */
    public function findAll(): array;

    /**
     * Find a company by ID.
     *
     * @param int $id
     * @return array | null
     */
    public function findById(int $id): array | null;

    /**
     * Update a company by ID.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array;

    /**
     * Delete a company by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find a company by email.
     *
     * @param string $email
     * @return array | null Returns the company data or null if not found.
     */
    public function findByEmail(string $email): array | null;

    /**
     * Find a company by CNPJ.
     *
     * @param string $cnpj The CNPJ of the company.
     * @return array | null Returns the company data or null if not found.
     */
    public function findByCnpj(string $cnpj): array | null;


    /**
     * Find a company by trade name.
     *
     * @param string $tradeName The trade (Nome Fantasia) name of the company.
     * @return array Returns the company data or null if not found.
     */
    public function findByTradeName(string $tradeName): array | null;


    /**
     * Find a company by legal name.
     *
     * @param string $legalName The legal (Razão Social) name of the company.
     * @return array Returns the company data or null if not found.
     */
    public function findByLegalName(string $legalName): array | null;


    /**
     * Find a company by phone.
     *
     * @param string $phone The phone number of the company.
     * @return array Returns the company data or null if not found.
     */
    public function findByPhone(string $phone): array | null;


    /**
     * Find multiple companies by size.
     *
     * @param string $size The size of the companies.
     * @return array | null Returns an array of companies or null if none found.
     */

    public function findBySize(string $size): array | null;

    public function findAllByUserId(): array;

}
