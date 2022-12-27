<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksheetController;
// use DB;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/some', function(){
    return User::where('users.id',6)->select('id','fullName','role',)->get();
});
Route::get('/someresult',[MarksheetController::class,'getmarksheet']);
// Route::get('/getresult', function() {
//         return 
// });
// Route::get('/public' , [MarksheetController::class ,'getReportData']);

