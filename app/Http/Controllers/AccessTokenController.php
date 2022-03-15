<?php

namespace App\Http\Controllers;

use Twilio\Jwt\AccessToken;
use Illuminate\Http\JsonResponse;
use Twilio\Jwt\Grants\VideoGrant;

class AccessTokenController extends Controller
{
    public function twilio() : JsonResponse
    {
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $apiKeySid = env('TWILIO_API_KEY');
        $apiKeySecret = env('TWILIO_API_SECRET');

        $identity = uniqid();

        // Create the Access Token
        $token = new AccessToken(
            $accountSid,
            $apiKeySid,
            $apiKeySecret,
            3600,
            $identity
        );

        // Grant the access to Video api
        $grant = new VideoGrant();
        $token->addGrant($grant);

        // Serialize the token as a JWT
        return response()->json($token->toJWT());

    }
}
