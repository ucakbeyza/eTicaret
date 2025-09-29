<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\HighCartTotalMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Cart;

class SendHighCartTotalMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-high-cart-total-mails';

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
        $this->info('High cart total mail command started.');
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
            $cartItems = Cart::with('product')
                ->where('user_id', $userRow->user_id)
                ->get();
            $total = $cartItems->sum(function($item) {
                return $item->quantity * $item->product->price;
            });
            if ($total >= 10000) {
                $user = User::find($userRow->user_id);
                if ($user) {
                    // mail gider
                    Mail::to($user->email)->send(new HighCartTotalMail($user, $total));
                }
            }
        }
    }
}