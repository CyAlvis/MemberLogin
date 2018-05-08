<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertUser;
use DB;
use Illuminate\Http\Request;
use Redis;

class LoginController extends Controller
{
    public function xss(Request $request)
    {

        $retData = [];
        $retData["data"] = $data;
        // return $data;
    }
    public function test()
    {

    }
    public function login(Request $request)
    {
        $psd = $request->psd;
        $result = \App\Models\Login::where('Account', $request->acc)->where('Password', $psd)->get(['id', 'Account', 'UserType', 'Name', 'Email', 'Phone', 'Agent']);
        // return count($result->toArray());
        if (count($result->toArray()) != 0) {
            $UserData = $result[0]->toArray();
            $token = $request->session()->get('_token');
            $session = json_encode(array_merge($request->session()->all(), $UserData));
            Redis::set($token, $session);
            $session = json_decode($session, true);
            $retData = [];
            $retData['ret'] = 0;
            $retData['token'] = $token;
            $retData['acc'] = $session['Account'];
            $retData['ut'] = $session['UserType'];
            return $retData;
        } else {
            return $ret = 1;
        }
    }

    public function logout(Request $request)
    {
        if (Redis::exists($request->token)) {
            Redis::del($request->token);
            $retData = [];
            $retData['ret'] = 0;
            return $retData;
        } else {
            $retData = [];
            $retData['ret'] = 1;
            return $retData;
        }
    }

    public function search(Request $request)
    {
        $tree = json_encode(\App\Models\logintree::all());
        Redis::set('tree', $tree);
        $tree = json_decode($tree, true);
        $UT = [];
        if ($request->agent == true) {array_push($UT, 0);}
        if ($request->member == true) {array_push($UT, 1);}
        if ($UT == null) {
            return $ret = 0;
        }
        $checkID = json_decode(Redis::get($request->token), true)['id'];
        $ID = \App\Models\Login::where('Account', $request->account)->get()->toArray()[0]['id'];
        $check = array_merge(\App\Models\logintree::SubUsers($checkID, $tree), (array) $checkID);
        if (in_array($ID, $check)) {
            $resultID = \App\Models\logintree::SubUsers($ID, $tree);
            $result = \App\Models\login::whereIn('id', $resultID)->whereIn('UserType', $UT)->get();
            $retData = [];
            $retData['id'] = $resultID;
            $retData['UT'] = $UT;
            $retData['out'] = $result;
            return $retData;
        } else {
            return $ret = 1;
        }

    }

    public function insert(InsertUser $request)
    {
        $psd = $request->psd;
        $result = new \App\Models\Login;
        $result->Account = $request->account;
        $result->Password = $psd;
        $result->UserType = $request->usertype;
        $result->Name = $request->name;
        $result->Email = $request->email;
        $result->Phone = $request->phone;

        $CurrUser = json_decode(Redis::get($request->token), true)['id'];
        $tree = new \App\Models\logintree;
        $tree->Name = $request->name;
        $tree->parentID = $CurrUser;

        DB::transaction(function () use ($result, $tree) {
            $result->save();
            $tree->save();
        });

    }

    public function update(Request $request)
    {
        $UserData = json_decode($request->getContent());
        $id = \App\Models\Login::where('Account', $UserData->account)->get()->toArray()[0]['id'];
        $result = \App\Models\Login::find($id);
        if (empty($UserData->psd) == false) {
            $passwordData = $UserData->psd->words;
            $psd = "$passwordData[0]$passwordData[1]$passwordData[2]$passwordData[3]";
            $result->Password = $psd;
        }
        $result->UserType = $UserData->usertype;
        $result->Name = $UserData->name;
        $result->Email = $UserData->email;
        $result->Phone = $UserData->phone;
        $result->save();

        $result = \App\Models\login::whereIn('id', $request->resultID)->whereIn('UserType', $request->resultUT)->get();
        return $result;
    }

    public function delete(Request $request)
    {
        $UserData = json_decode($request->getContent());
        $passwordData = $UserData->psd->words;
        $psd = "$passwordData[0]$passwordData[1]$passwordData[2]$passwordData[3]";
        $result = \App\Models\Login::where('Account', $request->account)->where('Password', $psd)->get(['id']);

        if (count($result->toArray()) != 0) {
            $id = $result->toArray()[0]['id'];
            $login = \App\Models\Login::find($id);
            $tree = \App\Models\logintree::find($id);
            DB::transaction(function () use ($login, $tree) {
                $login->delete();
                $tree->delete();
            });
            if (count($request->resultID) != 0) {
                $result = \App\Models\login::whereIn('id', $request->resultID)->whereIn('UserType', $request->resultUT)->get();
                return $result;
            } else {
                $retData = [];
                $retData["ret"] = 0;
                return $retData;
            }
        } else {
            $retData = [];
            $retData["ret"] = 1;
            return $retData;
        }
    }

    public function checklogin(Request $request)
    {
        if (Redis::exists($request->token)) {
            $curr_user = json_decode(Redis::get($request->token), true);
            $retData = [];
            $retData["loginAcc"] = $curr_user['Account'];
            $retData["usertype"] = $curr_user['UserType'];
            return $retData;
        }
    }
}
