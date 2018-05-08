<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function get(Request $request)
    {
        if ($request->type == 1) {
            $retData = \App\Test::find($request->id);
            return $retData;
        } else if ($request->type == 2) {
            $result = new \App\Test;
            $result->id = $request->id;
            $result->name = $request->name;
            $result->save();
        } else if ($request->type == 3) {
            $result = \App\Test::find($request->id);
            $result->name = $request->name;
            $result->save();
        } else {
            $retData = \App\Test::find($request->id);
            $retData->delete();
        }
    }

    public function post(Request $request)
    {
        if ($request->type == 1) {
            $retData = \App\Test::find($request->id);
            return $retData;
        } else if ($request->type == 2) {
            $result = new \App\Test;
            $result->id = $request->id;
            $result->name = $request->name;
            $result->save();
        } else if ($request->type == 3) {
            $result = \App\Test::find($request->id);
            $result->name = $request->name;
            $result->save();
        } else {
            $retData = \App\Test::find($request->id);
            $retData->delete();
        }
    }

    public function put(Request $request)
    {
        if ($request->type == 1) {
            $retData = \App\Test::find($request->id);
            return $retData;
        } else if ($request->type == 2) {
            $result = new \App\Test;
            $result->id = $request->id;
            $result->name = $request->name;
            $result->save();
        } else if ($request->type == 3) {
            $result = \App\Test::find($request->id);
            $result->name = $request->name;
            $result->save();
        } else {
            $retData = \App\Test::find($request->id);
            $retData->delete();
        }
    }

    public function delete($type,$id,$name)
    {
        if ($type == 1) {
            $retData = \App\Test::find($id);
            return $retData;
        } else if ($type == 2) {
            $result = new \App\Test;
            $result->id = $id;
            $result->name = $name;
            $result->save();
        } else if ($type == 3) {
            $result = \App\Test::find($id);
            $result->name = $name;
            $result->save();
        } else {
            $retData = \App\Test::find($id);
            $retData->delete();
        }
    }

}
