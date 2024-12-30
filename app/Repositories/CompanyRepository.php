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

    public function findAll(): array
    {
        return Company::where('id', '>', 0)->get()->toArray();
    }

    public function create(array $data): company
    {
        return Company::create($data);
    }

    public function findById(int $id): ?Company
    {
        return Company::where('id', $id)->first();
    }

    public function update(int $id, array $data): bool
    {
        $company = Company::where('id', $id)->first();
        if ($company) {
            return $company->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $company = Company::where('id', $id)->first();
        if ($company) {
            return $company->delete();
        }
        return false;
    }

    public function findByEmail(string $email): ?Company
    {
        return Company::where('email', $email)->first();
    }

    public function findByCnpj(string $cnpj): ?Company
    {
        return Company::where('cnpj', $cnpj)->first();
    }

    public function findByTradeName(string $tradeName): Company|null
    {
        return Company::where('trade_name', $tradeName)->first();
    }

    public function findByLegalName(string $legalName): Company|null
    {
        return Company::where('legal_name', $legalName)->first();
    }

    public function findByPhone(string $phone): Company|null
    {
        return Company::where('phone', $phone)->first();
    }

    public function findBySize(string $size): array
    {
        return Company::where('size', $size)->get()->toArray();
    }
}
