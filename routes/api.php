<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\StudentController;
use App\Mail\WelcomeUserMail;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ReqresController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

Route::get('/preview-mail', function () {
    $mail = new WelcomeUserMail('Nirmal', 1);

    return $mail->render();
});

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::get('/page', 'index');
    Route::get('/getdetail/{user}', 'getUser');
    Route::get('/customPage', 'paginateIndex');
    Route::post('/insert', 'store');
    Route::post('/login', 'login');
    Route::patch('/generate-token', 'generateApiToken')->middleware('auth:sanctum');

    Route::get('/group', 'logicalgrouping');
    Route::get('/condtion', 'conditionalclause');
    Route::get('/lock', 'locking');
    Route::get('/subquery', 'subqueryclause');
    Route::get('/limit', 'limitandoffset');
    Route::get('/whereany', 'whereany');
    Route::get('/onetoone', 'onetooneExample');
    Route::get('/hasmany', 'hasmanyExample');
    Route::get('/loading', 'eagerLoading');

    Route::get('/start-chain', 'chaining');
    Route::get('/start-batch', 'batchprocessUser');
    Route::get('/total-amount', 'Aggregations');
   
   Route::get('car/{user}', 'sendCarDetails');
});


Route::middleware(['auth:sanctum,token', 'role:admin'])->controller(UserController::class)->prefix('user')->group(function () {
    Route::get('/', 'wherebetween');
    Route::get('{user}', 'show');
    Route::patch('/update/{user}', 'update');
    Route::delete('/delete/{user}', 'destroy');
});

Route::controller(CarController::class)->prefix('car')->group(function () {
    Route::get('/', 'pricevalue');
    Route::get('/brand', 'brandname');
    Route::get('/clause', 'whenclause');
    Route::get('/jsonfetch', 'jsonclause');
    Route::get('/union', 'unions');
    Route::get('/whereall', 'whereall');
    Route::get('/inverse', 'inverseExample');
    Route::get('/hasmanyinverse', 'hasmanyInverseExample');

    Route::post('/cars/{car}/buy', 'buyCar')->middleware('auth:sanctum,token');
    Route::post('/carinsert', 'store');
    Route::post('/upsert', 'upsertquery');

    Route::patch('/generate-cartoken', 'generateCarToken')->middleware('auth:sanctum');
    Route::patch('/updatejson/{car}', 'updateusingjson');
});

Route::middleware('auth:sanctum,cartoken')->controller(CarController::class)->prefix('users/{user}/cars')->group(function () {
    Route::get('/join', 'jointable');
    Route::get('/{car}', 'show');
    Route::patch('/{car}', 'update');
    Route::delete('/{car}', 'destroy');
});

Route::controller(StudentController::class)->prefix('student')->group(function () {
    Route::get('/', 'index');
    Route::get('/append', 'appendExample');
    Route::get('/contains', 'containsExample');
    Route::get('/diff', 'diffExample');
    Route::get('/except', 'exceptExample');
    Route::get('/intersect', 'intersectExample');
    Route::get('/modelkeys', 'modelKeysExample');
    Route::get('/makevisible', 'makeVisibleExample');
    Route::get('/makehidden', 'makeHiddenExample');
    Route::get('/only', 'onlyExample');
    Route::get('/setvisible', 'setVisibleExample');
    Route::get('/sethidden', 'setHiddenExample');
    Route::get('/toquery', 'toQueryExample');

    Route::get('/transform', 'transformExample');
    Route::get('/undot', 'undotExample');
    Route::get('/times', 'timesExample');
    Route::get('/zip', 'zipExample');
    Route::get('/unless', 'unlessExample');
    Route::get('/when', 'whenExample');
    Route::get('/whenEmpty', 'whenEmptyExample');

    Route::get('/belongstomany', 'manytomanyExample');

    Route::post('/insert', 'store');
    Route::get('student-course/{student}', 'studentCourseMail');
});

Route::view('/upload', 'upload');

Route::post('/upload-image', [UploadController::class, 'uploadImage'])->name('image.upload');

Route::post('/upload-file', [UploadController::class, 'uploadFile'])->name('file.upload');

Route::controller(UploadController::class)->prefix('upload')->group(function () {
    Route::get('/fileContent', 'readFile');
    Route::get('/download', 'downloadFile');
    Route::get('/fileSize', 'getFileSize');
    Route::get('/fileVisibility', 'getVisibility');
    Route::get('/directory', 'createDirectory');
    Route::get('/rename', 'moveOrRename');
    Route::get('/custom', 'customDisk');

    Route::delete('/removeFile', 'deleteFile');
    Route::post('/storeFile', 'putFile');
});

Route::resource('customers', CustomerController::class);
Route::get('whereClause', [CustomerController::class, 'whereGet']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/events/', [EventController::class, 'index']);
    Route::get('/events/my', [EventController::class, 'myEvent']);
    Route::post('/events', [EventController::class, 'store']);
    Route::patch('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    Route::get('/tickets/', [TicketController::class, 'index']);
    Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

});

Route::controller(ApiController::class)->prefix('custom')->group(function () {
    Route::get('/middleware-user', 'middelwareUser');
    Route::get('/get-posts', 'getPosts');
    Route::post('/create-post', 'createPost');
    Route::put('/put', 'putPost');
    Route::delete('/remove', 'deletePost');
    Route::get('/get-photo', 'successfulMethod');
    Route::get('/get-user', 'body');
    Route::get('/client-error', 'clientErrorStatus');
    Route::get('post-comments', 'failedMethod');
    Route::get('user-name', 'collectMethod');
    Route::get('single-user', 'objectMethod');
    Route::get('objectwithforeach', 'objectWithforeach');
    Route::get('redirect', 'checkRedirect');
    Route::get('header', 'headerMethod');
    Route::get('myIp', 'ipDetail');
    Route::post('auth', 'login');
    Route::get('fetch-profile', 'profile');

});

Route::controller(ReqresController::class)->prefix('custom')->group(function () {
    Route::get('reqres-fetch', 'reqResApi');
    Route::get('single-user/{user}', 'singleUser');
    Route::post('create-user', 'createUser');
    Route::put('put-user', 'putUser');
    Route::patch('patch-user', 'patchUser');
    Route::delete('remove-user', 'removeUser');
    Route::post('magic-link', 'magicLink');

    Route::post('storeInDb', 'storeMyDb');
    Route::get('show-data/{reqres_id}', 'showReqres');
    Route::patch('update-user/{user_id}', 'updateUserDetail');
    Route::delete('delete-user/{user_id}', 'deleteUser');

    Route::get('nasa-getApi', 'showNasaRecored');
});