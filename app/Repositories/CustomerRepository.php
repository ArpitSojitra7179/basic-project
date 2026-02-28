<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Jobs\SendCustomerWelcomeMailJob;
use App\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{
	public function getAllCustomers() {
		return Customer::all();
	}

	public function getCustomerById($customer) {

		return Customer::findOrFail($customer);
	}

	public function createCustomer(array $data) {
		try {
			$customer = Customer::create($data);

			SendCustomerWelcomeMailJob::dispatch($customer);

			return response()->json([
				'Your data is Stored' => $customer,
			], 200);
		} catch (\Exception $e) {
			report($e);

			return response()->json([
				'message' => 'Something Went Wrong.',
			], 500);
		}
	}

	public function updateCustomer($customer, array $data) 
	{
		$customer = Customer::findOrFail($customer);
		return $update = $customer->update([$data]);
	}

	public function deleteCustomer($customer) {
		
		return Customer::destroy($customer);
	}

	public function getCustomerData() {
		try {
			$customer = Customer::where('name', 'Somil')->get();

			return response()->json([
				'all customer data fetch with name Somil' => $customer,
			], 200);
		} catch (\Exception $e) {
			report($e);

			return response()->json([
				'message' => 'Something Went Wrong.',
			], 500);
		}
	}

}