<?php namespace App\Core\Repositories;

interface OrganizationRepositoryInterface
{
    public function createOrganization(array $input);

    public function getOrganizations($select);

    public function getOrganization($id);

    public function updateOrganization($input, $organization);
}
