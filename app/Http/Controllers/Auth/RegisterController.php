<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
use Mail;
use App\Mail\CheckMailable;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return IlluminateContractsValidationValidator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [

            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
          //  'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    public function register(Request $request) {
        $input = $request->all();
        $validator = $this->validator($input);

     //   if ($validator->passes()){
            $user = $this->create($input)->toArray();
            $user['link'] = str_random(30);

            DB::table('user_activations')->insert(['id_user'=>$user['id'],'token'=>$user['link']]);

           // Mail::send('emails.activation', $user, function($message) use ($user){
            //    $message->to($user['email']);
              //  $message->subject('www.hc-kr.com - Activation Code');
         //   });
            Mail::to($user['email'])->send(new CheckMailable($user));

            return redirect()->to('login')->with('message',"We sent activation code. Please check your mail.");
      //  }
       // return back()->with('errors',$validator->errors());
    }

    public function userActivation($token){

        $check = DB::table('user_activations')->where('token',$token)->first();
        if(!is_null($check)){
            $user = User::find($check->id_user);
            if ($user->is_activated ==1){
                return redirect()->to('login')->with('success',"user are already actived.");

            }
            $user->update(['is_activated' => 1]);
            DB::table('user_activations')->where('token',$token)->delete();
            return redirect()->to('login')->with('success',"user active successfully.");
        }
        return redirect()->to('login')->with('Warning',"your token is invalid");
    }
}