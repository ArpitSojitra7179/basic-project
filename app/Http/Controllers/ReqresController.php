<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Models\Reqres;
use App\Models\User;

class ReqresController extends Controller
{
    protected $base_url = 'https://reqres.in';

    public function reqResApi() {
        try {
            $response = Http::withHeaders([
                'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            ])->get($this->base_url .'/api/users?page=2');

            // $response = json_decode($response);
            
            return response()->json([
                'Detail' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }


    public function createUser(Request $request) {
        try {
            $name = $request->name;
            $job = $request->job;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => 'dev_pub_05804e9ba35dd299e9c057d55d23b3cd',
            ])->post($this->base_url .'/app/users', [
                'name' => $name,
                'job' => $job,
            ]);

            $response = json_decode($response);

            return response()->json([
                'User created successfully' => $response,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function putUser(Request $request) {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'job' => 'required|string',
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            ])->put($this->base_url .'/api/users/2', [
                'name' => $data['name'],
                'job' => $data['job'],
            ]);

            return response()->json([
                'message' => 'User Detail Updated successfully',
                $response->json(),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function patchUser(Request $request) {
        try {
            $data = $request->validate([
                'job' => 'required|string',
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            ])->patch($this->base_url .'/api/users/1', [
                'job' => $data['job'],
            ]);

            return response()->json([
                'message' => 'User Detail Updated successfully',
                $response->json(),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function removeUser() {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            ])->delete($this->base_url .'/api/users/2');

            return response()->json([
                'message' => 'User Record Deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function magicLink(Request $request) {
        try {
            $email = $request->email;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            ])->post($this->base_url . '/api/auth/magic-link', [
                'email' => $email,
            ]);

            $response = json_decode($response);

            return response()->json([
                'Magic Link created successfully' => $response,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function storeMyDb(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'job' => 'required|string',
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            ])->post($this->base_url . '/api/users', [
                'name' => $data['name'],
                'job' => $data['job'],
            ]);

            if ($response->successful()) {

                $dbData = $response->json();
                \Log::info($dbData); 

                $user = Reqres::create([
                    'reqres_id' => $dbData['id'],
                    'name' => $dbData['name'],
                    'job' => $dbData['job'],
                ]);

                $externaldata =  [
                        'name' => $dbData['name'],
                        'job' => $dbData['job'],
                        'id' => $dbData['id'],
                        'createdAt' => $dbData['createdAt'],
                        '_meta' => [
                            'powered_by' => $dbData['_meta']['powered_by'],
                            'variant' => $dbData['_meta']['variant'],
                            'message' => $dbData['_meta']['message'],
                            'context' => $dbData['_meta']['context'],
                        ],
                    ];

                return response()->json([
                    'message' => 'User data successfully stored in both place',
                    'Database Data' => $user,
                    'external api data' => $externaldata,
                ], 200);
            }

            return response()->json([
                'message' => 'reqres API failed',
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function singleUser(User $user) {
        try {
            $singleUser = $user->reqres;

            // $response = Http::withHeaders([
            //     'x-api-key' => 'reqres_577fd386385645dab80f52118f7a26fa',
            // ])->get($this->base_url .'/api/users/1');

            // $response = json_decode($response);

            return response()->json([
                // 'External Users Detail' => $response,
                'Our User Detail' => $singleUser,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function updateUserDetail(Request $request, $user_id)
    {
        try {
            
            $validated = $request->validate([
                'name' => 'nullable|string',
                'job'  => 'nullable|string',
            ]);

            $reqres = Reqres::where('user_id', $user_id)->firstOrFail();

            $reqres->update($validated);

            return response()->json([
                'message' => 'User record updated successfully',
                'Detail'  => $reqres,
            \Log::info($reqres)
            ], 200);

        } catch (\Exception $e) {

            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function deleteUser($user_id)
    {
        try {
            $reqres = Reqres::where('user_id', $user_id)->firstOrFail();

            $reqres->delete();

            return response()->json([
                'reqres user deleted successfully' => $reqres,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function showReqres($reqres_id)
    {
        try {
            $fetch = Reqres::where('reqres_id', $reqres_id)->get();

            return response()->json([
                'Your details' => $fetch,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function showNasaRecored() {
        try {
            $response = Http::get('https://api.nasa.gov/insight_weather/?api_key=6J9gC5Ub9ueHkmCghpCGfAHKRndacB1k88N05aOC&feedtype=json&ver=1.0');

            $response = json_decode($response);

            return response()->json([
                'Details' => $response,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

}
