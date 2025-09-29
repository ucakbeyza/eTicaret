<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-abandoned-cart-reminders')->everyTenMinutes();

Schedule::command('telescope:prune')->daily();

Schedule::command('app:send-high-cart-total-mails')->hourly();