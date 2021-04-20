<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Storage::extend('google', function($app, $config) {
            $client = new \Google_Client();
            $client->setClientId('907448796963-mo2nr7ss0uqgcak4p70ck83si8pcmkjo.apps.googleusercontent.com');
            $client->setClientSecret('xujmuRbvhE7qz24IoAGNIw5o');
            $client->refreshToken('1//04AnPW3Oh7fUGCgYIARAAGAQSNwF-L9IrHbaJq7eruQh5IqL_XQ4DX5n4oRTDVvP4vhaJo4yf2IjgbV47CKq8Qba-Q7g_YlmHPVI');
            $service = new \Google_Service_Drive($client);

            $options = [];
            if(isset($config['teamDriveId'])) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }

            $adapter = new GoogleDriveAdapter($service, $config['folderId'], $options);

            return new \League\Flysystem\Filesystem($adapter);
        });
    }
}
