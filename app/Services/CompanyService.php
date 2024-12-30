<?php

namespace App\Services;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;

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

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function findAll(): array
    {
        return $this->companyRepository->findAll();
    }

    public function create(array $data): array
    {
        return $this->companyRepository->create($data)->toArray();
    }

    public function findById(int $id): array|null
    {
        $company = $this->companyRepository->findById($id);
        return $company ? $company->toArray() : null;
    }

    public function update(int $id, array $data): array
    {
        $this->companyRepository->update($id, $data);
        return $this->companyRepository->findById($id)->toArray();
    }

    public function delete(int $id): bool
    {
        return $this->companyRepository->delete($id);
    }

    public function findByEmail(string $email): array|null
    {
        $company = $this->companyRepository->findByEmail($email);
        return $company ? $company->toArray() : null;
    }

    public function findByCnpj(string $cnpj): array|null
    {
        $company = $this->companyRepository->findByCnpj($cnpj);
        return $company ? $company->toArray() : null;
    }

    public function findByTradeName(string $tradeName): array|null
    {
        $company = $this->companyRepository->findByTradeName($tradeName);
        return $company ? $company->toArray() : null;
    }

    public function findByLegalName(string $legalName): array|null
    {
        $company = $this->companyRepository->findByLegalName($legalName);
        return $company ? $company->toArray() : null;
    }

    public function findByPhone(string $phone): array|null
    {
        $company = $this->companyRepository->findByPhone($phone);
        return $company ? $company->toArray() : null;
    }

    public function findBySize(string $size): array|null
    {
        $company = $this->companyRepository->findBySize($size);
        return $company ? $company : null;
    }
}
