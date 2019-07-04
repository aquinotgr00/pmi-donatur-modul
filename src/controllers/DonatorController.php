<?php

namespace BajakLautMalaka\PmiDonatur\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use BajakLautMalaka\PmiDonatur\Donator;

use Illuminate\Support\Carbon;

use \App\User;
use BajakLautMalaka\PmiDonatur\Requests\StoreUserDonatorRequest;
use BajakLautMalaka\PmiDonatur\Requests\SigninPostRequest;
use Illuminate\Support\Facades\Auth;

class DonatorController extends Controller
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
        User    $users
    )
    {
        $this->donators = $donatorModel;
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

        // TODO: add custom user fields to config so that anyone could adjust
        $user = $this->users->create($request->only(['name', 'email', 'password']));
        $token = $user->createToken('Personal Access Token');

        // create donator
        $this->donators->create($request->except(['email', 'password', 'url_action']));

        // send email and token
        $data = [
            'email'   => $request->email,
            'content' => $request->url_action."/".$token->accessToken
        ];
        $this->donators->sendEmailAndToken($data);
        
        $response = [
            'access_token' => $token->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                                    $token->token->expires_at
                                )->toDateTimeString()
        ];

        return response()->json($response, 200);
    }

    /**
     * Login donator and create token
     *
     * @param BajakLautMalaka\PmiDonatur\Requests\SigninPostRequest $request
     * 
     * @return mixed|boolean
     */
    public function signin(SigninPostRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password'])))
            return response()->json([ "message" => "Account does not exist" ], 401);

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

        return response()->json($response, 200);
    }
    
    /**
     * Access token login donator verification to verified donator
     *
     * @param  string|null $token
     * @var boolean $access
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed|boolean $access
     * @return mixed|boolean
     */
    public function tokenMemberVerification(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $access = $this->access_token->verifyToken($request->token);

        $response = ["message" => "Token does not exist."];
        $status = 422;

        if (!is_null($access)) {
            $user = $this->donators->find($access->donator_id);

            if (!is_null($user)) {
                $tokenResult = $user->createToken('Member Grant Client');
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                $response = [
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                                        $tokenResult->token->expires_at
                                    )->toDateTimeString()
                ];
                $status = 201;
            }
        }

        return response()->json($response, $status);
    }

    /**
     * Access token login donator to get oauth Client
     *
     * @param  string $data
     *
     * @return string $access_token
     */
    public function accessTokenDonator($donatorId)
    {
        $token = sha1(Carbon::now()->timestamp."".$donatorId);
        $access_token = $this->access_token->create([
            'donator_id' => $donatorId,
            'token' => $token,
            'expired_at' => Carbon::now()->addWeeks(1)
        ]);
        return $access_token->token;
    }

     /**
     * Access token login donator verification to verified donator
     *
     * @param  string|null $token
     * @var boolean $access
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed|boolean $access
     * @return mixed|boolean
     */
    public function verifyDonatorAccessToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $accessToken = $this->access_token->verifyToken($request->token);

        $response = ["message" => "Token does not exist."];
        $status = 422;

        if (!is_null($accessToken)) {
            $donator = $this->donators->find($accessToken->donator_id);

            if (!is_null($donator)) {
                $tokenResult = $donator->createToken('Member Grant Client');
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                $response = [
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ];
                $status = 201;
            }
        }
        
        return response()->json($response, $status);
    }

    /**
     * sending email and access token to Forgot / reset password
     *
     * @param  array  $data
     *
     * @return void
     */
    public function sendEmailAndTokenReset($data)
    {
        dispatch(new \BajakLautMalaka\PmiDonatur\Jobs\SendEmailDonatorResetPassword($data));
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
            'email' => 'required|string|email',
        ]);

        $response = [
            'message'=>'Email not found'
        ];
        $status = 404;

        $donator = $this->donators->findForPassport($request->email);
        if (!is_null($donator)) {
            $token = sha1(Carbon::now()->timestamp."".$donator->id);
            $this->password_reset->create(['token' => $token, 'email' => $request->email]);

            $response = [
                'access_token' => $token
            ];
            if (!is_null($request->url_act)) {
                $data=[
                    'email' => $request->email,
                    'content' => $request->url_act."/".$token
                ];
                $this->sendEmailAndTokenReset($data);
            }
            $status = 200;
        }
        
        return response()->json($response, $status);
    }
}