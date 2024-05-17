<?php

use App\Http\Controllers\SearchController;
use Router\Route;
use App\Libraries\Response;


Route::post('/products/([0-9]*)', [new SearchController, 'index']);

Route::pathNotFound(function ($path) {
    return Response::httpCode(Response::HTTP_NOT_FOUND, Response::$statusTexts[Response::HTTP_NOT_FOUND], 'Not Found Page');
});

Route::run('/reactapi');







// Route::put('/put/([0-9]*)',function($id){
//     return $id;
// });
// Route::get('/home/([0-9]*)', [new HomeController, 'index']);

// Route::get('/home/([0-9]*)', [new Model, 'index']);

// Route::get('/home/([0-9]*)',function($id){
//     $home = new HomeController;
//     $model = new Model;
//     return $home->index(IFunction::numberFilter($id)) . '--- Model : ' . $model->index();

// });

// Route::post('/post-me', function (Request $request) {
//     $data = json_decode($request->getContent(), true);
//     $firstName = $data['firstName'];
//     return $firstName;
// });

// Route::delete('/delete/([0-9]*)', function (int $id) {
//     return $id;
// });

// Route::get('/sample/([0-9]*)', function (int $id) {
//     return $id;
// });


// Route::prefix('/api', function () {
//     Route::post('/post-me', function (Request $request) {
//         $data = json_decode($request->getContent(), true);
//         $firstName = $data['firstName'];
//         return $firstName;
//     });
//     Route::delete('/delete/([0-9]*)', function (int $id) {
//         return $id;
//     });
// });


// namespace App\Http\Controllers;

// use App\Http\Controller;
// use App\Models\Products;
// use Helpers\IFunction;
// use App\Libraries\Request;

// class SearchController extends Controller
// {
//     public function index(Request $request, int $id)
//     {
//         $data = $request->getContent();
//         $data = [
//             'title' => $data->title,
//             'desc' => $data->desc,
//             'decimal' => $data->decimal
//         ];

//         $rules = [
//             'title' => 'required',
//             'desc' => 'required|email',
//             'decimal' => 'required|numeric'
//         ];

//         $errors = $request->validate($data, $rules);

//         if (!empty($errors)) {
//             foreach ($errors as $error) {
//                 return $error . "\n";
//             }
//         }

//         return $id;

//     }
// }
