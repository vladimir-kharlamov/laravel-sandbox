<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class JWTCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:request_auth_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request to get auth token with tymon/jwt-auth';

    protected $authToken = null;
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*  Perform request to get token */
        $response = $this->getToken();

        if ($response->status() !== \Illuminate\Http\Response::HTTP_OK) {
            $this->error('Something wrong. Status code ' . $response->status());
            dd($response->json());
        }

        if (array_key_exists('access_token', $response->json())) {
            $this->authToken = $response->json()['access_token'];

            $this->info('Token have been received');

            $this->info('Get current user from AuthController:');
            print_r($this->getCurrentUser()->json());

            $this->info('Get current user from Request instance:');
            print_r($this->getCurrentUserFromRequest()->json());
        } else {
            $this->error('Something went wrong!');
        }

        /* Get refresh token */
        if ($result = $this->getRefreshToken()) {
            $this->info('Get refresh access token');
            $this->info('Current access token:');
            print_r($this->authToken);

            $this->newLine();
            $this->info('Refresh token:');
            print_r($result->json());
        }
    }

    /**
     * Get current user from AuthController
     * @return \GuzzleHttp\Promise\PromiseInterface|Response
     */
    protected function getCurrentUser() {
        $url = URL::to('/') . "/api/auth/me";

        return Http::asJson()
            ->withToken( 'Bearer ' . $this->authToken) // 'Authorization' param of headers
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url);
    }

    /**
     * Get current user from Illuminate\Http\JsonResponse instance
     * @return \GuzzleHttp\Promise\PromiseInterface|Response
     */
    protected function getCurrentUserFromRequest() {
        $url = URL::to('/') . "/api/user";

        return Http::asJson()
            ->withToken( 'Bearer ' . $this->authToken)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get($url);
    }

    /**
     * @return Response
     */
    protected function getToken(): Response {
        $url = URL::to('/') . "/api/auth/login";

        return Http::asJson()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url, [
                'url' => $url,
                /* Test incorrect credentials */
//                'email' => 'not email',
//                'someField' => 'notInRule',

                /* Correct credentials */
                'email' => 'test@example.com',
                'password' => 'password'
            ]);
    }

    protected function getRefreshToken() {
        $url = URL::to('/') . "/api/auth/refresh";

        return Http::asJson()
            ->withToken( 'Bearer ' . $this->authToken)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url);
    }

}
