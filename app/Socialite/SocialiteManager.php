<?php

namespace App\Socialite;

class SocialiteManager extends \Laravel\Socialite\SocialiteManager
{
    protected function createYahooDriver()
    {
        $config = $this->app['config']['services.yahoo'];

        return $this->buildProvider('App\Socialite\Two\YahooProvider', $config);
    }
}