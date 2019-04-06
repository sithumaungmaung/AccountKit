<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

class AccountKitController extends Controller
{
	/**
     * $appId
     * @var [int]
     */
	protected $appId;

    /**
     * [$appSecret description]
     * @var [string]
     */
    protected $appSecret;

   	/**
    * [$tokenExchangeUrl description]
    * @var [type]
    */
   	protected $tokenExchangeUrl;

   	/**
    * [$endPointUrl description]
    * @var [type]
    */
   	protected $endPointUrl;

   	/**
    * [$userAccessToken description]
    * @var [type]
    */
   	public $userAccessToken;

   	/**
    * [$refreshInterval description]
    * @var [type]
    */
   	protected $refreshInterval;

   	/**
    * [__construct description]
    */
   	public function __construct()
   	{
   		$this->appId            = config('accountkit.app_id');
   		$this->client           = new GuzzleHttpClient();
   		$this->appSecret        = config('accountkit.app_secret');
   		$this->endPointUrl      = config('accountkit.end_point');
   		$this->tokenExchangeUrl = config('accountkit.tokenExchangeUrl');
   	}


  	/**
   * [login description]
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  	public function login(Request $request)
  	{
  		$url = $this->tokenExchangeUrl.'grant_type=authorization_code'.
  		'&code='. $request->get('code').
  		"&access_token=AA|$this->appId|$this->appSecret";

  		$apiRequest = $this->client->request('GET', $url);

  		$body = json_decode($apiRequest->getBody());

  		$this->userAccessToken = $body->access_token;
		$this->refreshInterval = $body->token_refresh_interval_sec;

  		$user = $this->getData();

  		$tokenResult = $user->createToken('Personal Access Token');
        
        $token = $tokenResult->token;
        // dd($token);
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
  	}

  	public function getData()
  	{
  		$request = $this->client->request('GET', $this->endPointUrl.$this->userAccessToken);

  		$data = json_decode($request->getBody());

  		$userId = $data->id;

  		$phone = isset($data->phone) ? $data->phone->number : null;

  		$email = isset($data->email) ? $data->email->address : null;

  		$user = $this->findOrCreate($userId,$phone,$email);

  		return $user;
  	}

  	public function findOrCreate($userId,$phone,$email)
  	{
		$user = User::where('account_kit_id', $userId)->first();
       	
       	if (!isset($user)) {
            $user = User::create([
                'account_kit_id' => $userId,
                'email' => $email,
                'phone' => $phone,
            ]);
        }

        return $user;
  	}

  	public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
