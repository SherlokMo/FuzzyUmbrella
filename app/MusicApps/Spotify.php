<?php
namespace App\MusicApps;


use App\MusicApps\Token;

class Spotify extends music
{

    protected $HTTP_ENDPOINTS =[
        'Auth' =>[
            "Client-Credentials" => "https://accounts.spotify.com/api/token"
        ]
    ];

    public function __construct()
    {

        parent::__construct();

        $this->setAuth();
    }

    protected function getOauth($type = " Basic ") : string
    {
        return $type . base64_encode(env('SPOTIFY_CLIENT_ID') . ":" . env('SPOTIFY_SECRET_ID'));
    }


}


?>
