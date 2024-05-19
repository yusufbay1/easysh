<?php

use Router\Route;
use App\Libraries\Response;


Route::pathNotFound(function ($path) {
    return Response::httpCode(Response::HTTP_NOT_FOUND, Response::$statusTexts[Response::HTTP_NOT_FOUND], 'Not Found Page');
});

Route::run('/reactapi');
