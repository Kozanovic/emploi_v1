<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJwtMiddlewaer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            $token = JWTAuth::getToken();
            if (!$token) {
                return  response()->json(['error' => 'token not Provided']);
            }

            // parstoken permet de extraire le token á aprtire de l‘entéte de request souvent sur le Bearer Token
            // authenticate permet de dècode le tone est extraire le id est chercher le user associè de ce id sur la base de donner 
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException) {
            return response()->json(['error' => 'token invalid'], 404);
        } catch (TokenExpiredException) {
            return response()->json(['error' => 'token expired'], 404);
        }
        $request->attributes->add(['user' => $user]);
        return $next($request);
    }
}
