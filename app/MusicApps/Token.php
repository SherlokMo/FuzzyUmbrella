<?php

namespace App\MusicApps;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;

class Token
{
    /**
     * Is an Oauth token key
     *
     * @var [string]
     */
    private string $token;

    /**
     * App id represent the id of the app (Spotify, Deezer, ... etc)
     *
     * @var [int]
     */
    private $app_id;


    public function __construct($app_id)
    {
        $this->app_id = $app_id;
    }

    public function hasValidToken() : bool
    {
        $token = DB::table('application_server_auth')
                ->where('application_id','=',$this->app_id)
                ->where('expires_at', '>', Carbon::now('Africa/Cairo')->format("Y-m-d H:i:s"))
                ->get(['Oauth_token as token'])
                ->toArray();
        if($token)
        {
            $this->setToken($token[0]->token);
            return true;
        }

        return false;
    }

    /**
     * Sends Server to server Client creditnaials request to get Token with timeout of 3600s
     *
     * @param [type] $token
     * @return boolean
     */
    public function sendClientCredintials($Oauth, $url)
    {
        $response =
        Http::withHeaders(['Authorization'=>$Oauth])
        ->asForm()->post($url, [
            'grant_type'=>"client_credentials"
        ]);

        $this->storeToken($response);
    }

    public function getToken() : string
    {
        return $this->token;
    }

    private function storeToken(Response $response) : bool
    {

        if($response->successful())
        {
            $this->setToken($response->json()['access_token']);
            DB::insert('INSERT INTO application_server_auth (Oauth_token, application_id, expires_at) values (?, ?, ?)', [$this->token, $this->app_id, Carbon::now('Africa/Cairo')->addHour(1)->format("Y-m-d H:i:s")]);
        }

        return $response->successful();

    }

    private function setToken($token) : void
    {
        $this->token = $token;
    }

}

?>
