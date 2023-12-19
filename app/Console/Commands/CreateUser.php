<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user {total=1 : So luong nguoi dung}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo User';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::factory($this->argument('total'))->create();
        return Command::SUCCESS;
    }
}
