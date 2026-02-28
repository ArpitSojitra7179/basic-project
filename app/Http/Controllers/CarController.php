<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Car;


class CarController extends Controller
{

    public function pricevalue()
    {
        try {
            $details = Car::where('price', '>', 500000)->orderBy('car_name', 'desc')->get();

            dd($details->toArray());

            $details->dumpRawSql();

            $unorderdcar = $details->reorder()->get();

            return response()->json([
                "message" => "This cars price is above 100000",
                'Cars' => $unorderdcar,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function brandname()
    {
        try {
            $brand = Car::select('brand_name')->groupBy('brand_name')->having('brand_name', 'Like', 'T%')->get();

            return response()->json([
                'message' => 'Car Brand name start with T.',
                'brands' => $brand,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function jointable()
    {
        try {
            $join = User::join('cars', 'users.id', '=', 'cars.user_id')->get();

            return response()->json([
                'message' => 'Users and Cars Details.',
                'Details' => $join,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function whenclause(Request $request)
    {
        try {
            $carname = $request->string('car_name');

            $condition = Car::when($carname, function ($query) {
                $query->orderBy('car_name', 'desc');
            })->get();

            return response()->json([
                'Details' => $condition,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function whereall() {
       try {
            $cars = Car::where('price', '>', 2000000)->whereAll(['created_at', 'updated_at',], 'Like', '%10%')->get();

            return response()->json([
                'Cars' => $cars,
            ], 200);
       } catch (\Exception $e) {
           report($e);

           return response()->json([
            'message' => 'Something went wrong.'
           ], 500);
       }
    }

    public function jsonclause()
    {
        try {
            $json = Car::whereJsonContains('details->status', 'sold')->get();

            return response()->json([
                'Car details' => $json,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function unions() {
        try {
            \Log::info("Union work successfully.");
            $first = Car::where('price', '<', '2000000');

            $second = Car::where('brand_name', 'Like', 'T%')->union($first)->get();

            return response()->json([
                'Cars' => $second,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function buyCar(Request $request, Car $car)
    {
        try {
            $result = DB::transaction(function () use ($car) {
                $user = auth()->user();

                if (!$user) {
                    return ['error' => "User not authenticated."];
                }

                $car = Car::where('id', $car->id)->lockForUpdate()->first();
                if (!$car) {
                    return ['error' => "Car not found."];
                }

                if (!is_null($car->user_id)) {
                    return ['error' => "This car is already sold."];
                }

                if ($user->credit < $car->price) {
                    return ['error' => "Not enough credit to buy this car."];
                }

                $user->decrement('credit', $car->price);

                $car->user_id = $user->id;
                $car->details = [
                    'status' => 'sold',
                    'buyer'  => $user->name,
                ];
                $car->save();

                return [
                    'message' => 'Car purchased successfully!',
                    'user' => $user,
                    'car' => $car,
                ];
            });

            return response()->json([
                'Info' => $result,
            ]);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function store(Request $request, Car $car)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|integer',
                'brand_name' => 'required|string',
                'car_name' => 'required|string',
                'price' => 'required|integer',
                'details' => 'nullable|array',
            ]);

            $car = Car::create($validated);
            $token = $car->createToken('CarApiToken')->plainTextToken;

            return response()->json([
                'message' => 'car deta inserted successfully.',
                'Car details' => $car,
                'Car Token' => $token,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function show(User $user, Car $car)
    {
        try {
            return response()->json([
                'All Cars Details' => $car,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function update(Request $request, Car $car)
    {
        try {
            $validated = $request->validate([
                'brand_name' => 'nullable|string',
                'car_name' => 'nullable|string',
                'price' => 'nullable|integer',
            ]);

            $car->update($validated);

            return response()->json([
                'message' => 'Car deta updated successfully.',
                'Details' => $car,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function updateusingjson(Car $car)
    {
        try {
            $details = $car->details;
            $details['status'] = 'sold';

            $car->details = $details;
            $car->save();

            return response()->json([
                'updated details' => $car->details,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ]);
        }
    }

    public function upsertquery()
    {
        try {
            $upsertdetails = Car::upsert(
                [
                    [
                        'user_id' => 1,
                        'brand_name' => 'Mahindra',
                        'car_name' => 'XUV700',
                        'price' => 2000000
                    ],
                ],
                ['user_id', 'brand_name', 'car_name'],
                ['price']
            );

            return response()->json([
                'Details' => $upsertdetails,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function destroy(User $user, Car $car)
    {
        try {
            if ($car->user_id != $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Book not found for this student.'
                ], 404);
            }

            $car->delete();

            return response()->json([
                'message' => 'Car deta deleted successfully.',
                'car' => $car,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function generateCarToken()
    {
        try {
            $car = auth()->user();

            $cartoken = str()->random(32);
            $car->update(["car_token" => $cartoken]);

            return response()->json([
                'message' => 'Token generated.',
                'Token' => $cartoken,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function inverseExample() {
        try {
            $cars = Car::with('user')->get();

            return response()->json([
                'car and users details' => $cars,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function hasmanyInverseExample() {
        try {
            $car = Car::find(1);

            return response()->json([
                'name' => $car->user->name,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
