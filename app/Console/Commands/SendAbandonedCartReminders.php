<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\AbandonedCartReminderMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-abandoned-cart-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Abandoned cart reminder command started.');
        $twoHoursAgo = Carbon::now()->subHours(2);
        $tenMinutesAgo = Carbon::now()->subHours(2)->subMinutes(10);

        //sepetinde ürün olan kullanıcıların kontrolü
        $users = DB::table('carts')
            ->select('user_id')
            ->where('created_at', '<=', $twoHoursAgo)
            ->where('created_at', '>', $tenMinutesAgo)//saat kontrolü
            ->groupBy('user_id')
            ->get();

        foreach ($users as $userRow) {

            //kullanıcının son 2 saatte alışveriş yapmış mı
            $hasPurchased = DB::table('orders')
                ->where('user_id', $userRow->user_id)
                ->where('created_at', '>=', $twoHoursAgo)
                ->exists();

            if (!$hasPurchased) {
                $user = User::find($userRow->user_id);
                if ($user) {
                    // mail gider
                    Mail::to($user->email)->send(new AbandonedCartReminderMail($user));
                }
            }
        }
        return Command::SUCCESS; //komutun başarıyla tamalandığını cron a bildiriyor
    }
}
