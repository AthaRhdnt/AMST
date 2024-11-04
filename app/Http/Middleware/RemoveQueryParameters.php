<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveQueryParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // // Check if the request is a GET request and if it has query parameters
        // if ($request->isMethod('GET') && $request->query()) {
        //     // Check if the search parameter exists in the query
        //     if (!$request->has('search')) {
        //         return redirect()->to($request->url()); 
        //     }
        // }
        
        // // Check if the request is a GET request and if it has query parameters
        // if ($request->isMethod('GET') && $request->query()) {
        //     // Check if the search parameter exists in the query
        //     if (!$request->has('search') && !$request->has('start_date') && !$request->has('end_date') && !$request->has('entries')) {
        //         return redirect()->to($request->url());
        //     }
        // }

        // Check if the request is a GET request
        if ($request->isMethod('GET')) {
            // Store query parameters in session if they are present
            if ($request->has('search')) {
                $request->session()->put('search', $request->input('search'));
                return $next($request);
            }

            if ($request->has('start_date')) {
                $request->session()->put('start_date', $request->input('start_date'));
            }

            if ($request->has('end_date')) {
                $request->session()->put('end_date', $request->input('end_date'));
            }

            if ($request->has('entries')) {
                $request->session()->put('entries', $request->input('entries'));
                // Prevent redirect when entries are being set
                return $next($request);
            }

            // If there are any query parameters, redirect to the URL without them
            if ($request->query()) {
                return redirect()->to($request->url());
            }
        }

        return $next($request);
    }
}
