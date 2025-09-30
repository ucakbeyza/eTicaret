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

        $carts = Cart::with('product')
            ->select('user_id', DB::raw('SUM(quantity * products.price) as total'))
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->whereBetween('carts.created_at', [$twoHoursAgo, $tenMinutesAgo])
            ->groupBy('user_id')
            ->having('total', '>=', 10000)
            ->get();

        foreach ($carts as $cart) {
            $user = User::find($cart->user_id);
            if ($user) {
                Mail::to($user->email)->send(new HighCartTotalMail($user, $cart->subtotal));
            }
        }
    }
}