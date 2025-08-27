<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RateLimiterServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(60)->by(
        $request->user()?->id ?: $request->ip()
      );
    });

    // Örnek: login için ayrı limiter
    RateLimiter::for('login', function (Request $request) {
      return Limit::perMinute(5)->by($request->ip());
    });
  }
}









