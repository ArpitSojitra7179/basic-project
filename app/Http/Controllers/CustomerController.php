<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\CustomerRepositoryInterface;

class CustomerController extends Controller
{
    protected $customerRepo;

    public function __construct(CustomerRepositoryInterface $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    public function index() {
        return $this->customerRepo->getAllCustomers();
    }

    public function store(Request $request) {
        return $this->customerRepo->createCustomer($request->all());
    }

    public function show($customer) {
        return $this->customerRepo->getCustomerById($customer);
    }

    public function update($customer, Request $request) {
        return $this->customerRepo->updateCustomer($customer, $request->all());
    }

    public function destroy($customer) {
        return $this->customerRepo->deleteCustomer($customer);
    }

    public function whereGet() {
        return $this->customerRepo->getCustomerData();
    }
}
