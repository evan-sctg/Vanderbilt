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


use Illuminate\Http\Request;


//main interface
Route::get('/', function () {
    return view('interface');
});


//individual project detail page
Route::get('/project/{projectid}',  'ProjectsController@ViewProjectDetails')->where('projectid', '[0-9]+');


//csfr token refresh endpoint
Route::post('/token', function (Request $request) {
    return response()->json([
    'token' => csrf_token(),
    'status' => 'OK'
    ]);
});


//endpoint for list view
Route::post('/view-list', 'ProjectsController@ViewProjects_datatable')->name('ViewList');

//endpoint for grid view
Route::post('/view', 'ProjectsController@ViewProjects')->name('ViewGrid');

//endpoint for scanning for new projects
Route::post('/scan', 'ProjectsController@Scan')->name('ScanProjects');


