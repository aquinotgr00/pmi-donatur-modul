<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use BajakLautMalaka\PmiDonatur\Donator;
use BajakLautMalaka\PmiDonatur\PasswordReset;

use Illuminate\Support\Carbon;

use \App\User;
use BajakLautMalaka\PmiDonatur\Http\Requests\StoreUserDonatorRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\SigninPostRequest;
use Illuminate\Support\Facades\Auth;

class DonatorApiController extends Controller
{
    /**
     * Create a new parameter.
     *
     * @var mixed donators
     */
    protected $donators;

    /**
     * Create a new parameter.
     *
     * @var mixed passwordResets
     */
    protected $passwordResets;
    
    /**
     * Create a new parameter.
     *
     * @var mixed users
     */
    protected $users;

    /**
     * Create a new parameter.
     *
     * @var mixed jobs
     */
    protected $jobs;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Donator $donatorModel,
        PasswordReset $passwordResets,
        User    $users
    )
    {
        $this->donators = $donatorModel;
        $this->passwordResets = $passwordResets;
        $this->users    = $users;
    }

    /**
     * Create user and then donator
     *
     * @param \BajakLautMalaka\PmiDonatur\Requests\StoreUserDonatorRequest $request
     * @return mixed
     */
    public function signup(StoreUserDonatorRequest $request)
    {
        // manually hash the password
        $request->merge([
            'password' => bcrypt($request->password)
        ]);

        // handle image
        $image = $this->donators->handleDonatorPicture($request->file('image'));
        $request->merge([
            'image' => $image
        ]);

        // TODO: add custom user fields to config so that anyone could adjust
        $user = $this->users->create($request->only(['name', 'email', 'password']));
        $token = $user->createToken('Personal Access Token');
        
        // add user id to request
        $request->merge([
            'user_id' => $user->id
        ]);

        // create donator
        $this->donators->create($request->except(['email', 'password', 'url_action']));

        // send email and token
        $data = [
            'email'   => $request->email,
            'content' => 'Thanks for joining us.'
        ];
        $this->donators->sendEmailAndToken($data);
        
        $response = [
            'access_token' => $token->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                                    $token->token->expires_at
                                )->toDateTimeString()
        ];

        return response()->success($response);
    }

    /**
     * Login donator and create token
     *
     * @param BajakLautMalaka\PmiDonatur\Requests\SigninPostRequest $request
     * 
     * @return mixed
     */
    public function signin(SigninPostRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password'])))
            return response()->fail([ "message" => "Account does not exist" ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        $response = [
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                                    $tokenResult->token->expires_at
                                )->toDateTimeString()
        ];

        return response()->success($response);
    }

    /**
     * requets to generate token for forgot password member using api and generate email
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed
     *
     */
    public function createTokenForgotPassword(Request $request)
    {
        $request->validate([
            'email'      => 'required|string|email',
            'url_action' => 'required'
        ]);

        $response = [
            'message' => 'Email not found'
        ];

        $donator = $this->users->where('email', $request->email)->first();
        if (!$donator) {
            return response()->fail($response);
        }

        $token = sha1(Carbon::now()->timestamp."".$donator->id);
        $this->passwordResets->create(['token' => $token, 'email' => $request->email]);

        $data = [
            'email'   => $request->email,
            'content' => $request->url_action."/".$token
        ];
        $this->donators->sendEmailAndTokenReset($data);

        $response = [
            'reset_password_token' => $token
        ];
        
        return response()->success($response);
    }

    /**
     * Change password using verified token
     *
     * @param   \Illuminate\Http\Request  $request
     * 
     * @return mixed
     *
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            "token"    => "required",
            "password" => "required|confirmed"
        ]);

        $response = ["message" => "Email / token not valid"];

        $check = $this->passwordResets->where('token', $request->token)->first();
        if (!$check) {
            return response()->fail($response);
        }

        $user = $this->users->where('email', $check->email)->first();
        // TODO: check for old password.
        $this->users->where('email', $check->email)->update(['password' => bcrypt($request->password)]);
        // TODO: delete password reset token after successfully changed password.
        $token = $user->createToken('New Personal Access Token');
        
        $data = [
            'email' => $check->email,
            'content' => 'Password kamu berhasil di ubah.'
        ];
        $this->donators->sendEmailSuccess($data);

        $response = [
            "message"      => "Password sucessfully changed.",
            "access_token" => $token->accessToken
        ];

        return response()->success($response);
    }

    /**
     * Edit the donator data
     *
     * @param Request $request
     * @param integer $id
     * @return mixed
     */
    public function updateDonatorProfile(Request $request, $id)
    {
        // TODO: add auth guard
        // $this->guard('auth:api');
        $response = [
            'message' => 'Donator not found.'
        ];

        $donator = $this->donators->find($id);
        if (!$donator) { return response()->fail($response); }
        $donator->update($request->all());
        
        $response['message'] = 'Your data sucessfully changed.';

        return response()->success($response);
    }
}