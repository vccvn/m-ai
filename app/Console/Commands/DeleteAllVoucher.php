<?php

namespace App\Console\Commands;

use App\Repositories\Promotions\VoucherRepository;
use Illuminate\Console\Command;

class DeleteAllVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:vouchers {type? : Loại voucher}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xoá tất cả voucher';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(VoucherRepository $voucherRepository)
    {
        if($type = $this->argument('type')){
            $voucherRepository->where('type', $type);
        }

        if(count($vouchers = $voucherRepository->get()) > 0){
            foreach($vouchers as $voucher){
                $voucher->delete();
            }
        }
        $this->info("ĐÃ xoá voucher");
        return Command::SUCCESS;
    }
}
