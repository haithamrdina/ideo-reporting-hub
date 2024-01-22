<?php

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCourseList;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeList;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeUsersList;
use App\Http\Integrations\Docebo\Requests\DoceboLpList;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Http\Integrations\Speex\Requests\SpeexUserId;
use App\Http\Integrations\Speex\SpeexConnector;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // $doceboConnector = new DoceboConnector();

    // $paginator1 = $doceboConnector->paginate(new DoceboLpList('CDG'));
    // // $paginator2 = $doceboConnector->paginate(new DoceboGroupeUsersList('986'));
    // $result1 = [];
    // foreach($paginator1 as $pg){
    //     $data = $pg->dto();
    //     $result1 = array_merge($result1, $data);
    // }

    // $paginator3 = $doceboConnector->paginate(new DoceboLpsEnrollements($result1));
    // $result3 =[];
    // foreach($paginator3 as $pg){
    //     dump($pg->json());
    //     dd($pg->dto());
    //     $data = $pg->dto();
    //     $result3 = array_merge($result3, $data);
    // }
    // dd($result3);

    // return view('welcome');
    $speexConnector = new SpeexConnector();
    $response = $speexConnector->send(new SpeexUserId('s.kamel@cig.pe'));
    dd($response->dto());
});

Route::as('admin.')->group(function(){
    require __DIR__.'/central-auth.php';
});
