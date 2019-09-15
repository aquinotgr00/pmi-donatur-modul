<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use BajakLautMalaka\PmiDonatur\Donator;
use BajakLautMalaka\PmiDonatur\PasswordReset;
use BajakLautMalaka\PmiDonatur\Http\Requests\SigninPostRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\StoreUserDonatorRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateDonatorRequest;
use \App\User;

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
        Donator       $donatorModel,
        PasswordReset $passwordResets,
        User          $users
    )
    {
        $this->donators       = $donatorModel  ;
        $this->passwordResets = $passwordResets;
        $this->users          = $users         ;

        $this->middleware('auth:api')->only([
            'profile',
            'updateDonatorProfile',
        ]);
    }

    public function list(Request $request, Donator $donators)
    {
        $donators = $this->handleSearch($request,$donators);
        $donators = $donators->paginate(15);
        foreach ($donators as $donator) {
            $donator->donations;
            $donator->user;
        }
        $admins = $donators;
        return response()->success(compact('admins'));
    }

    public function handleSearch(Request $request,$donators)
    {
        if ($request->has('s')) {
            $donators = $donators->where('name','like','%'.$request->s.'%')
            ->orWhere('phone','like','%'.$request->s.'%')
            ->orWhere('created_at','like','%'.$request->s.'%');

            $donators = $donators->whereHas('user', function ($query) use ($request) {
                $query->where('users.email', 'like', '%' . $request->s . '%');
            });
        }
        return $donators;
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

        if ($request->has('image_file')) {
          $image = $request->image_file->store('donator-picture', 'public');
          $request->request->add([
              'image' => $image
          ]);
        }

        // TODO: add custom user fields to config so that anyone could adjust
        $user  = $this->users->create($request->only(['name', 'email', 'password']));
        $token = $user->createToken('PMI');
        
        // add user id to request
        $request->merge([
            'user_id' => $user->id
        ]);

        // create donator
        $donator = $this->donators->create($request->except(['email', 'password', 'url_action']));

        // send email and token
        $data = [
            'email'   => $request->email,
            'content' => 'Thanks for joining us.'
        ];
        $this->donators->sendEmailAndToken($data);
        
        return response()->success([
            'access_token' => $token->accessToken,
            'donator_id'=>$donator->id,
            'volunteer_id'=>null
        ]);
    }
    
    public function show($id)
    {
        $donator = $this->donators->find($id);
        $donator->donations;
        $donator->user;
        foreach ($donator->donations as $donation) {
            $donation->campaign;
        }

        return response()->success($donator);
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

        $user        = $request->user();
        $tokenResult = $user->createToken('PMI');
        $token       = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        $response = [
            'access_token' => $tokenResult->accessToken,
            'donator_id'   => $user->donator ? $user->donator->id:null,
            'volunteer_id' => null
        ];
        
        if ($user->volunteer) {
            if ($user->volunteer->verified) {
              $response['volunteer_id'] = $user->volunteer->id;
            } else {
                return response()->fail(['message' => 'Please wait us to verify you.']);
            }
        }

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
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'      => 'required|string|email',
            'url_action' => 'required'
        ]);

        $response = [
            'message' => 'Email not found'
        ];

        $user = $this->users->where('email', $request->email)->first();
        if (!$user)
            return response()->fail($response);

        $token = sha1(Carbon::now()->timestamp."".$user->id);
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

        $tokenReset = $this->passwordResets->where('token', $request->token)->first();
        if (!$tokenReset && Carbon::parse($tokenReset->updated_at)->addMinutes(720)->isPast()) {
            return response()->fail($response);
        }

        $user = $this->users->where('email', $tokenReset->email)->first();
        $this->users->where('email', $tokenReset->email)->update(['password' => bcrypt($request->password)]);
        $token = $user->createToken('PMI');
        
        $data = [
            'email'   => $tokenReset->email,
            'content' => 'Password kamu berhasil di ubah.'
        ];
        $this->donators->sendEmailSuccess($data);

        $response = [
            "message"      => "Password sucessfully changed.",
            "access_token" => $token->accessToken
        ];

        return response()->success($response);
    }

    public function profile()
    {
        $user    = auth()->user();
        $donator = $user->donator;
        if (!$donator)
            return response()->fail(['message' => 'Donator not found.']);
        if ($donator->donations)
            $donator->donations;

        return response()->success($donator);
    }

    /**
     * Edit the donator data
     *
     * @param Request $request
     * @return mixed
     */
    public function updateProfile(Request $request)
    {
        $response = [
            'message' => 'Donator not found.'
        ];

        $image = $request->image->store('donator-picture', 'public');
        $request = new Request($request->all());
        $request->merge([
            'image' => $image
        ]);

        $user = auth()->user();

        $donator = $this->donators->where('user_id', $user->id)->first();

        if (!$donator)
            return response()->fail($response);

        $donator->update($request->all());
        $donator->donations;

        $response['message']    = 'Your data sucessfully changed.';

        $response['donator']       = $donator;

        return response()->success($response);
    }

    public function update($id, UpdateDonatorRequest $request)
    {
        $donator = Donator::find($id);
        if (! is_null($donator) ) {

            $user = User::firstOrCreate(
            [
                'id' => $donator->user_id 
            ],
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt('password')
            ]
        );
            if ($user->email != $request->email) {
                $user->update([
                    'email' => $request->email
                ]);
            }

            $donator->update([
                'name' => $request->name,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'user_id' => $user->id
            ]);


        }
        $donator->donations;
        return response()->success($donator);
    }
}