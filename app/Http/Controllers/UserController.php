<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Throwable;

use App\Models\User;
use App\Models\Car;
use App\Events\UserRegistered;
use App\Mail\WelcomeUserMail;
use App\Mail\UserLoginMail;
use App\Mail\UsersCarDetails;

use App\Jobs\SendWelcomeEmail;
use App\Jobs\ProcessSingleUser;
use App\Jobs\StepOneJob;
use App\Jobs\StepTwoJob;
use App\Jobs\StepThreeJob;

class UserController extends Controller
{
    
    public function index() {
        try {
            $minutes = 60;
            $cookieValue = 'User_cookie';

            $users = User::orderBy('id')->cursorPaginate(20);

            return response()->json([
                'Users' => $users,
                'message' => 'Cookie set successfully created.',
            ], 200)->cookie('session_id', $cookieValue, $minutes);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function getUser(User $user) {
        try {
            $name = $user->name;

            return response()->json([
                'detail' => $name,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function paginateIndex(Request $request) {
        try {
            $data = collect([
                ['id'=>1,'name'=>'A'],
                ['id'=>2,'name'=>'B'],
                ['id'=>3,'name'=>'C'],
                ['id'=>4,'name'=>'D'],
                ['id'=>5,'name'=>'E'],
                ['id'=>6,'name'=>'F'],
            ]);

            $perPage = 2;
            $currentPage = $request->page ?? 1;

            $currentItems = $data->slice(
                ($currentPage - 1) * $perPage,
                $perPage
            )->values();

            $paginator = new LengthAwarePaginator(
                $currentItems,
                $data->count(),
                $perPage,
                $currentPage,
                [
                    'path' => url()->current(),
                    'query' => $request->query(),
                ]
            );

            return view('/user', compact('paginator'));
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function wherebetween()
    {
        try {

            $users = User::wherebetween('id', array(1, 30))->get();

            return response()->json([
                "users" => $users
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                "message" => "Something went wrong."
            ], 500);
        }
    }

    public function whereany()
    {
        try {
            $users = User::where('role', 'admin')->whereAny(['name', 'email',], 'Like', '%a%')->get();

            return response()->json([
                'Users' => $users,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function limitandoffset()
    {
        try {
            $users = User::offset(6)->limit(4)->get();

            return response()->json([
                'Users' => $users,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function logicalgrouping()
    {
        try {
            $info = User::where('name', '=', 'Gopal')->where(function ($query) {
                $query->where('role', '=', 'admin')->orWhere('credit', '>', 1000);
            })->get();

            return response()->json([
                'Users' => $info,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function conditionalclause(Request $request)
    {
        try {
            $credit = $request->integer('credit');

            $condition = User::when($credit, function ($query) {
                $query->orderBy($credit);
            }, function ($query) {
                $query->orderBy('name');
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

    public function locking()
    {
        try {
            $lock = User::where('id', '=', 7)->lockForUpdate()->get();

            return response()->json([
                'Details' => $lock,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function subqueryclause()
    {
        try {
            $subquery = User::where(function ($query) {
                $query->select('car_name')
                    ->from('cars')
                    ->whereColumn('cars.user_id', 'users.id')
                    ->orderByDesc('cars.created_at')
                    ->limit(1);
            }, '!=', 'Mahindra')->get();

            return response()->json([
                'Details' => $subquery,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function onetooneExample()
    {
        try {
            $user = User::with('car')->get();

            return response()->json([
                'users and car details' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function hasmanyExample()
    {
        try {
            $user = User::find(8)->cars()->where('brand_name', 'Mahindra')->get();

            return response()->json([
                'all Mahindra cras and its owner' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function eagerLoading()
    {
        try {

            $load = User::whereHas('cars', function ($q) {
                $q->where('price', '>', 1900000);
            })->with(['cars' => function ($q) {
                $q->where('price', '>', 1900000);
            }])->get();

            return response()->json([
                'Details' => $load,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ]);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $minutes = 60;
            $cookieValue = 'sample_value';

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'role' => 'required|string',
                'credit' => 'required|integer',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'credit' => $validated['credit'],
                'password' => bcrypt($validated['password']),
            ]);

            // SendWelcomeEmail::dispatch($user);
            
            event(new UserRegistered($user));

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Data inserted and email queued!',
                'users' => $user,
                'token' => $token,
            ], 200)->cookie('session_id', $cookieValue, $minutes);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                "message" => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {

            // sleep(10);
            // SendWelcomeEmail::dispatch($user)->delay(now()->addMinutes(1))->onQueue('sendmail');

            event(new UserRegistered($user));

            return response()->json([
                'user details' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                "message" => "Something went wrong."
            ], 500);
        }
    }

    public function sendCarDetails($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $cars = $user->cars;

            Mail::to($user->email)->send(new UsersCarDetails($user, $cars));

            return response()->json([
                'Car details email sent!',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function login(Request $request, User $user)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => "Invalid Credentials",
                ], 401);
            }

            $token = $request->user()->createToken('API Token')->plainTextToken;

            Mail::to($user->email)->send(new UserLoginMail($user->name, $user->id));

            return response()->json([
                'Details' => $credentials,
                'Token' => $token,
            ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function chaining()
    {
        try {
            Bus::chain([
                new StepOneJob(),
                new StepTwoJob(),
                new StepThreeJob(),
            ])->onQueue('chain')->dispatch();

            return response()->json([
                'message' => 'Job Chain dispatched!',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function batchprocessUser()
    {
        $users = User::all();

        $jobs = [];

        foreach ($users as $user) {
            $jobs[] = new ProcessSingleUser($user->id);
        }

        $batch = Bus::batch($jobs)->then(function (Batch $batch) {
            logger("Batch successfully Completed: " . $batch->id);
        })->catch(function (Batch $batch, Throwable $e) {
            logger("Batch Error in ID: {$batch->id}");
        })->finally(function (Batch $batch) {
            logger("Batch Finally Executed: {$batch->id}");
        })->name('Process All Users')->dispatch();

        return response()->json([
            'message' => 'User batch started!',
            'batch_id' => $batch->id,
        ]);
    }

    public function Aggregations() 
    {
        try {
            $total_amount = User::where('role', 'admin')->sum('credit');

            return response()->json([
                'All User total credit' => $total_amount,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'email' => 'nullable|email',
                'role' => 'nullable|string',
                'credit' => 'nullable|integer',
            ]);

            $user->update($validated);

            return response()->json([
                "user" => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                "message" => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return response()->json([
                'message' => 'user data deleted successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                "message" => "Something went wrong."
            ], 500);
        }
    }

    public function generateApiToken()
    {
        try {
            $user = auth()->user();

            $apiToken = str()->random(32);

            $user->update(["api_token" => $apiToken]);

            return response()->json([
                "message" => "Token generated.",
                'token' => $apiToken,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                "message" => "Something went wrong."
            ], 500);
        }
    }
}
