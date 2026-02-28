<?php

namespace App\Interfaces;


interface CustomerRepositoryInterface
{
    public function getAllCustomers();
    public function getCustomerById($customer);
    public function createCustomer(array $data);
    public function updateCustomer($customer, array $data);
    public function deleteCustomer($customer);
    public function getCustomerData();
}
