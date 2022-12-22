<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use App\Models\VerificationToken;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller as Controller;

class GoogleController extends Controller
{
    /**
     * Gets a google client
     *
     * @return \Google_Client
     * INCOMPLETE
     */
    private function getClient():\Google_Client
    {
        // load our config.json that contains our credentials for accessing google's api as a json string
        $configJson = base_path().'/config.json';

        // define an application name
        $applicationName = 'TriviCare Natural Cosmetics';

        // create the client
        $client = new \Google_Client();
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($configJson);
        $client->setAccessType('offline'); // necessary for getting the refresh token
        $client->setApprovalPrompt ('force'); // necessary for getting the refresh token
        // scopes determine what google endpoints we can access. keep it simple for now.
        $client->setScopes(
            [
                \Google\Service\Oauth2::USERINFO_EMAIL,
                \Google\Service\Oauth2::OPENID,
            ]
        );
        $client->setIncludeGrantedScopes(true);
        return $client;
    } // getClient

        /**
     * Return the url of the google auth.
     * FE should call this and then direct to this url.
     *
     * @return JsonResponse
     * INCOMPLETE
     */
    public function getAuthUrl(Request $request):JsonResponse
    {
        /**
         * Create google client
         */
        $client = $this->getClient();

        /**
         * Generate the url at google we redirect to
         */
        $authUrl = $client->createAuthUrl();

        /**
         * HTTP 200
         */
        return response()->json($authUrl, 200);
    } // getAuthUrl


        /**
     * Login and register
     * Gets registration data by calling google Oauth2 service
     *
     * @return JsonResponse
     */
    public function postLogin(Request $request):JsonResponse
    {

        /**
         * Get authcode from the query string
         * Url decode if necessary
         */
        $authCode = urldecode($request->input('auth_code'));

        /**
         * Google client
         */
        $client = $this->getClient();

        /**
         * Exchange auth code for access token
         * Note: if we set 'access type' to 'force' and our access is 'offline', we get a refresh token. we want that.
         */
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        /**
         * Set the access token with google. nb json
         */
        $client->setAccessToken(json_encode($accessToken));

        /**
         * Get user's data from google
         */
        $service = new \Google\Service\Oauth2($client);
        $userFromGoogle = $service->userinfo->get();

        /**
         * Select user if already exists
         */

        $user = User::where('provider_name', 'google')
            ->where('provider_id', $userFromGoogle->id)
            ->first();

        $email = User::where('email', $userFromGoogle->email)->first();

        /**
         */
        if(!$user && !$email) {
            $user = User::create([
                'provider_id' => $userFromGoogle->id,
                'provider_name' => 'google',
                'google_access_token_json' => json_encode($accessToken),
                'email' => $userFromGoogle->email,
                'password' => bcrypt('C0d3c@mp'),
            ]);
            
            //generate random string
            $rand_token = openssl_random_pseudo_bytes(16);
            //change binary to hexadecimal
            $token = bin2hex($rand_token);

            VerificationToken::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);
            
            //generate email verification
            $mailData = [
                'title' => 'Muchas gracias por registrarte en Trivicare.com',
                'body' => 'Gracias por registrarte en Trivicare.com. Ahora puedes disfrutar de todos nuestros servicios.',
                'email' => $user->email,
                'url' => 'https://api.trivicare.com/verify-email/' . $token,
            ];

            Mail::to($user->email)->send(new VerificationMail($mailData));

            return response()->json([
                'message' => 'User Created',
                'user' => $user,
            ], 200);
            
        }else if(!$user && $email) { 
            $user->provider_id = $userFromGoogle->id;
            $user->provider_name = 'google';
            $user->google_access_token_json = json_encode($accessToken);
            $user->save();

            return response()->json([
                'message' => 'Email Already Exists',
                'user' => $user,
            ], 200);
            
        } else {
            $user->google_access_token_json = json_encode($accessToken);
            $user->save();

            return response()->json([
                'message' => 'Access Token Updated',
                'user' => $user,
            ], 200);
        }


        //$token = $user->createToken("Google")->accessToken;
    } // postLogin


        /**
     * Returns a google client that is logged into the current user
     *
     * @return \Google_Client
     */
    private function getUserClient():\Google_Client
    {
        /**
         * Get Logged in user
         */
        $user = User::where('id', '=', auth()->guard('api')->user()->id)->first();

        /**
         * Strip slashes from the access token json
         * if you don't strip mysql's escaping, everything will seem to work
         * but you will not get a new access token from your refresh token
         */
        $accessTokenJson = stripslashes($user->google_access_token_json);

        /**
         * Get client and set access token
         */
        $client = $this->getClient();
        $client->setAccessToken($accessTokenJson);

        /**
         * Handle refresh
         */
        if ($client->isAccessTokenExpired()) {
            // fetch new access token
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $client->setAccessToken($client->getAccessToken());

            // save new access token
            $user->google_access_token_json = json_encode($client->getAccessToken());
            $user->save();
        }

        return $client;
    } // getUserClient


        /**
     * Get meta data on a page of files in user's google drive
     *
     * @return JsonResponse
     */
    public function login(Request $request):JsonResponse
    {
        /**
         * Get google api client for session user
         */
        $client = $this->getUserClient();

        /**
         * Create a service using the client
         * @see vendor/google/apiclient-services/src/
         */
        // $service = new \Google\Service\Drive($client);

        /**
         * The arguments that we pass to the google api call
         */
        // $parameters = [
        //     'pageSize' => 10,
        // ];

        /**
         * Call google api to get a list of files in the drive
         */
        //$results = $service->files->listFiles($parameters);

        /**
         * HTTP 200
         */
        return response()->json($client, 200);
    }

    

}
