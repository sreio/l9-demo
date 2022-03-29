<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;

class AuthController extends Controller
{
    /**
     * Register api
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|unique:users,name',
            'email' => 'bail|required|email|unique:users,email',
            'password' => 'bail|required',
            'c_password' => 'bail|required|same:password',
        ], [
            'name.required' => '请填写名称',
            'name.unique' => '用户名称已存在',
            'email.required' => '请填写邮箱',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱已注册',
            'password.required' => '请填写密码',
            'c_password.required' => '请再次填写确认密码',
            'c_password.same' => '两次密码不一致',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::query()->create($input);
        $success['token'] =  $user->createToken('sreio')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->success($success);
    }

    /**
     * Login api
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => '请填写邮箱',
            'email.email' => '邮箱格式不正确',
            'password.required' => '请填写密码',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('sreio')->plainTextToken;
            $success['name'] =  $user->name;
            return $this->success($success);
        }
        else{
            return $this->failed("用户不存在");
        }
    }

}
