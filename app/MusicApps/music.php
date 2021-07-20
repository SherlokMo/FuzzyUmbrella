<?php

namespace App\MusicApps;

use App\MusicApps\Interface\IMusic;
use Carbon\Carbon;

use App\MusicApps\Token;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;

abstract class music implements IMusic{


    protected $token;

    protected $app_id;

    protected $tokenManager;

    /**
     * Set by each class on it's On
     *
     * @var [type]
     */
    protected $HTTP_ENDPOINTS;

    abstract protected function getOauth() : string;

    public function __construct()
    {
        $this->tokenManager = new Token($this->getAppId());
    }

    protected function setAuth(){

        if(!$this->tokenManager->hasValidToken())
        {
            return $this->tokenManager->sendClientCredintials($this->getOauth(), $url = $this->getHttpEndPoint(['Auth','Client-Credentials']));
        }

    }

    private function getAppId(): int
    {
        if(!$this->app_id)
        {
            $this->app_id = DB::table('applications')->where('title', "=", $this->getAppName())->select(['id'])->get()[0]->id;
        }

        return $this->app_id;
    }

    private function getAppName() : string
    {
        $dispatcher = explode('\\', static::class);
        return end($dispatcher);
    }

    protected function getHttpEndPoint($path)
    {
        $result = $this->HTTP_ENDPOINTS ?? [];

        foreach($path as $endpoint)
        {
            $result = $endpoint ? $result[$endpoint] : false;
        }

        return $result;

    }

}


?>
