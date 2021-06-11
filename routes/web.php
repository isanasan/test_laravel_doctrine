<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/tasks', 'App\Http\Controllers\TaskController@getIndex');

Route::group(['mddleware' => ['web']], function () {
    Route::get('test-user', function (\Doctrine\ORM\EntityManagerInterface $em) {
        $user = new \App\Entities\User('Francesco', 'francescomalatest@live.it');
        $user->setPassword(bcrypt('12345678'));

        $em->persist($user);
        $em->flush();
    });

    Route::get('login', function () {
        return view('login');
    });

    Route::post('login', function (\Illuminate\Http\Request $request) {
        if (\Auth::attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ])) {
            return redirect('/');
        }
    });

    Route::get('logout', function () {
        \Auth::logout();
        return redirect('login');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::resource('/', 'App\Http\Controllers\TaskController');
    });
});
