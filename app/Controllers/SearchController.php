<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Libraries\Request;

class SearchController extends Controller
{
    public function index(Request $request, int $id)
    {
        $req = $request->getContent();
        $data = [
            'title' => $req->title,
            'desc' => $req->desc,
            'decimal' => $req->decimal,
            'password' => $req->password,
            'password_confirm' => $req->password_confirm
        ];

        $rules = [
            'title' => 'required|in:a,f',
            'desc' => 'required|email|max:10',
            'decimal' => 'required|numeric',
            'password' => 'required|confirmed'
        ];

        $errors = $request->validate($data, $rules);

        if (!empty($errors)) {
            return json_encode($errors);
        }

        return $id;
    }
}
