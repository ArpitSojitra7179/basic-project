<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Services\ApiClientService;
 
class ApiController extends Controller
{

    protected $api;

    public function __construct(ApiClientService $api)
    {
        $this->api = $api->getClient();
    }

    public function middelwareUser()
    {
        try {
            $response = $this->api->get('https://jsonplaceholder.typicode.com/users');

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' =>false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPosts()
    {
        try {
            $response = Http::withHeaders([
                'content-type' => 'application/json',
            ])->get('https://jsonplaceholder.typicode.com/posts?userId=2');

            $response = json_decode($response);

            return response()->json([
                'User all posts' => $response,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function createPost(Request $request)
    {
        try {

            $title = $request->title;
            $body = $request->body;
            $userId = $request->userId;

            $response = Http::post('https://jsonplaceholder.typicode.com/posts', [
                'title' => $title,
                'body' => $body,
                'userId' => $userId,
            ]);

            $response = json_decode($response);

            return response()->json([
                'Post Created' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
        
    }

    public function putPost(Request $request) {
       try {
        $data = $request->validate([
            'title'   => 'required|string',
            'body'    => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $response = Http::put(
            'https://jsonplaceholder.typicode.com/posts/1',
            [
                'title'  => $data['title'],
                'body'   => $data['body'],
                'userId' => $data['user_id'],
            ]
        );

        return response()->json([
            'message' => 'Post updated successfully',
            'data'    => $response->json(),
        ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function deletePost() {
        try {
            $response = Http::delete('https://jsonplaceholder.typicode.com/posts/1');

            $response = json_decode($response);

            return response()->json([
                'Post deleted' => $response,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function successfulMethod()
    {
        $response = Http::timeout(3)->get('https://jsonplaceholder.typicode.com/photos/5001');
        if ($response->successful()) {
            return $response->json();
        } elseif ($response->clientError()) {
            return 'Client Error: ' . $response->status();
        } elseif ($response->serverError()) {
            return 'Server Error: ' . $response->status();
        }
        
    }

    public function body()
    {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/users');

            $rawBody = $response->body();

            return response()->json([
                'rawbody' => $rawBody,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function clientErrorStatus()
    {   
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/users/11');

            $clientError = $response->clientError();

            return response()->json([
                'client error status' => $clientError,
                'status' => $response->status(),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
        
    }

    public function failedMethod() {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts/10/comments');

        if ($response->successful()) {
            return $response->json();
        } else {
            \Log::error('Request Failed: ' . $response->failed());
        }
    }

    public function collectMethod() {
        $response = Http::get('https://jsonplaceholder.typicode.com/users');

        $users = $response->collect();

        $names = $users->pluck('name');

        return response()->json([
            'Users Name' => $names,
            'Status Code' => $response->status(),
        ]);
    }

    public function objectMethod() {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/users/1');

            $user = $response->object();

            return [
                'name' => $user->name,
                'email' => $user->email,
                'city' => $user->address->city,
            ];
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function objectWithforeach()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/users');

        $users = $response->object();

        foreach ($users as $user) {
            echo $user->name;
        }
    }

    public function checkRedirect()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/users');

        if ($response->redirect()) {
            return 'This API redirected the request';
        } else {
            return 'Normal response, no redirect';
        }
    }

    public function headerMethod()
    {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/posts');

            $headers = $response->headers();
            
            return response()->json([
                'all header' => $headers,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
        
    }

    public function ipDetail()
    {
        try {
            $ip = Http::get('https://free.freeipapi.com/api/json/110.226.31.13');
        
            \Log::info($ip);

            $ip = json_decode($ip);

            return response()->json([
                'Details' => $ip,
            ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function login()
    {
       try {
            $response = Http::post('https://dummyjson.com/auth/login', [
            'username' => 'emilys',
            'password' => 'emilyspass',
        ]);
            \Log::info($response);
        if ($response->successful()) {
            return response()->json([
                'token' => $response->json('token'),
            ], 200);
        }
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Login failed',
            ], 401);  
        }
    }

    public function profile()
    {
        try {
            $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJlbWlseXMiLCJlbWFpbCI6ImVtaWx5LmpvaG5zb25AeC5kdW1teWpzb24uY29tIiwiZmlyc3ROYW1lIjoiRW1pbHkiLCJsYXN0TmFtZSI6IkpvaG5zb24iLCJnZW5kZXIiOiJmZW1hbGUiLCJpbWFnZSI6Imh0dHBzOi8vZHVtbXlqc29uLmNvbS9pY29uL2VtaWx5cy8xMjgiLCJpYXQiOjE3NzA3ODQxMDAsImV4cCI6MTc3MDc4NzcwMH0.XUsDetVRvdQqCoUQpDLiEoAYCYics_mtk_ql_e7d8NI';

            $response = Http::withToken($token)->get('https://dummyjson.com/auth/me');

            return response()->json([
                $response->json(),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
