<?php

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
    return view('login-user');
});
Route::get('show', array('as' => 'show', function () {
    return view('show');
}));

Route::get('/blade', function () {
    return view('layouts.app');
});

Route::get('phpinfo', function () {
    return view('phpinfo');
});
Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout');

Route::get('test', function () {
    $startID = [16];
    //  var_dump($startID);
    $howclose = 3;
    echo "$howclose 等親<br>";
    $result = [];
    $temp = [];
    $partnerID = \App\relation::whereIn('PartnerID', $startID)->get()[0]->toArray()['id'];
    array_push($startID, $partnerID);
    for ($i = 0; $i < $howclose; $i++) {
        //  var_dump($temp);
        //  var_dump($startID);
        $childrenID = array_map(function ($n) {
            return $n['id'];
        }, \App\relation::whereIn('DadID', $startID)->orwhereIn('MomID', $startID)->get()->toArray());
        $childrenID = array_diff($childrenID, $temp);
        //  var_dump($childrenID);
        $childrenParID = array_map(function ($n) {
            return $n['id'];
        }, \App\relation::whereIn('PartnerID', $childrenID)->get()->toArray());
        //  var_dump($childrenParID);
        $input = \App\relation::whereIn('id', $startID)->get()->toArray();
        $DadID = array_map(function ($n) {
            if ($n['DadID'] != 0) {
                return $n['DadID'];
            }
        }, $input);
        $MomID = array_map(function ($n) {
            if ($n['MomID'] != 0) {
                return $n['MomID'];
            }
        }, $input);
        $parentID = array_merge($DadID, $MomID);
        $parentID = array_diff($parentID, $temp);
        //  var_dump($parentID);
        $temp = array_merge($temp, $startID);
        $startID = array_merge($childrenID, $childrenParID, $parentID);
        $result = array_merge($result, $startID);
    }
    // return $childrenID;
    $result = \App\relation::whereIn('id', $startID)->get();
    $result = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $result);
    return $result;
});

Route::group(['middleware' => ['checktoken']], function () {

    Route::post('token', 'LoginController@token');

    Route::get('edit-user', function () {
        return view('edit-user');
    })->name('toedit');

    Route::post('insert', 'LoginController@insert');

    Route::post('update', 'LoginController@update');

    Route::post('delete', 'LoginController@delete');

    Route::post('search', 'LoginController@search');

    Route::get('search-user', function () {
        return view('search-user');
    });

    Route::post('checklogin', 'LoginController@checklogin');

});

Route::get('get', 'TestController@get');

Route::post('post', 'TestController@post');

Route::put('put', 'TestController@put');

Route::delete('delete/{type}/{id}/{name}', 'TestController@delete');
