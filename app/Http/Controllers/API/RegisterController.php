<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\VerifyRequest;
use App\Models\User;
use App\SMS\KaveNegar;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->all();
        if (User::where(['mobile' => $input['mobile']])->first()) {
            return $this->sendError('Duplicated.', ['error' => 'This Mobile Number Exist'], 400);
        }
        $input['password'] = bcrypt($input['password']);
        $input['verify_code'] = random_int(11111, 99999);
        $user = User::create($input);

        if ($_ENV['APP_ENV']  == 'local'){
            $input['verified'] = 1;
        }


        $sms = new KaveNegar($user->mobile , __('messages.verify_sms' , ['code' => $user->verify_code]));
        if (!$sms){
            return $this->sendError('SMS Error.', ['error' => __('messages.sms_error')], 400);
        }
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'Verification Code Sent Successfully.');
    }

    public function verify(VerifyRequest $request): JsonResponse
    {
        $input = $request->all();
        $user = User::where(['mobile' => $input['mobile'] , 'verify_code' => $input['verify_code']])->first();
        if ($user) {
            $user->verified = 1;
            $user->save();
            $success['token'] =  $user->createToken($_ENV['APP_NAME'])->plainTextToken;
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User Verified Successfully.');
        }
        return $this->sendError('Duplicated.', ['error' => 'Mobile Number Or Code Is Incorrect'], 400);
    }

    /**
     * Login api
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['mobile' => $request['mobile'], 'password' => $request['password']])) {
            $user = Auth::user();
            $success['token'] = $user->createToken($_ENV['APP_NAME'])->plainTextToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success, 'User LoggedIn Successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 403);
        }
    }
}
