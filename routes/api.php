<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/forgotpassword',[AuthController::class,'ForgotPassword']); 
Route::post('/resetpassword',[AuthController::class,'ResetPassword']); 



Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/update',[AuthController::class,'update']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/save_game',[GameController::class,'store']);

    Route::get('/user_game_record/{id}',[GameController::class,'show']);
    Route::get('/user_game_record/search/{gamename}',[GameController::class,'search']);

    // admin data points 
    Route::get('/usercount',[AuthController::class,'countUsers']); 
    Route::get('/totalplays',[GameController::class,'index']); 
    Route::get('/most_played_game',[GameController::class,'mostPlayedGame']); 
    Route::get('/most_played_category',[GameController::class,' mostPlayedCategory']); 
    Route::get('/games_played',[GameController::class,'countUniqueGames']); 

    Route::post('/played_games_with_date',[GameController::class,'countUniqueGamesWithDate']); 
    Route::post('/total_plays_with_date',[GameController::class,'countGamesWithDate']); 

});
