<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\UserEngagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TrackUserEngagement
{
    /**
     * The user engagement service.
     *
     * @var \App\Services\UserEngagementService
     */
    protected $engagementService;

    /**
     * Create a new middleware instance.
     *
     * @param  \App\Services\UserEngagementService  $engagementService
     * @return void
     */
    public function __construct(UserEngagementService $engagementService)
    {
        $this->engagementService = $engagementService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start session if user is logged in and no engagement session exists
        if (Auth::check() && !Session::has('engagement_id')) {
            $this->engagementService->startSession($request);
        }
        
        // Track page visit if session exists
        if (Auth::check() && Session::has('engagement_id')) {
            $this->engagementService->trackPageVisit($request->path());
        }

        $response = $next($request);
        
        // Update last activity timestamp
        if (Auth::check() && Session::has('engagement_id')) {
            $this->engagementService->updateActivity();
        }

        return $response;
    }
}