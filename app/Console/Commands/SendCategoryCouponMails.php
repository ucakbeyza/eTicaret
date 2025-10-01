<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Mail\CategoryCouponMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SendCategoryCouponMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-category-coupon-mails {categoryId}';

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
        $categoryId = $this->argument('categoryId');
        $category = Category::find($categoryId);
        if (!$category) {
            $this->error("Category with ID {$categoryId} not found.");
            return 1;
        }

        //ilgili kategoride alışveriş yapan kullanıcılar
        $users = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.category_id', $categoryId)
            ->distinct() //tekrar eden user_id'leri engeller
            ->pluck('orders.user_id');

        foreach ($users as $userId) {
            $user = User::find($userId);
            $cuponCode = $this->generateCouponCode();
            //kullanıcıya mail gönder
            Mail::to($user->email)->send(new CategoryCouponMail($user, $category, $cuponCode));
            $this->info("Coupon mail sent to user ID {$userId} for category {$category->name}.");
        }
        return 0;
    }
    private function generateCouponCode()
    {
        return 'KUPON' . rand(10000, 99999);
    }
}
