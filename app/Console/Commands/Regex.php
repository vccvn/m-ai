<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Regex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regex {subject?} {pattern?} {--pattern=null} {--subject=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiem tra cuoi bang regex';

    protected $pattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';

    protected $subject = 'Test 1234509876';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $regex = $this->argument('regex');

        if($pa = $this->argument('pattern'))
            $pattern = $pa;
        elseif (($po = $this->option('pattern')) != 'null')
            $pattern = $po;
        else
            $pattern = $this->pattern;
        if($sa = $this->argument('subject'))
            $subject = $sa;
        elseif(($so = $this->option('subject')) != 'null')
            $subject = $so;
        else
            $subject = $this->subject;
        echo "Pattern: $pattern\n";
        echo "Subject: $subject \n";
        preg_match_all($pattern, $subject, $matches);
        print_r($matches);
        // echo $this->💕();
        return Command::SUCCESS;
    }


    /**
     * make 💕
     * @param 💕 $💕
     * @return 💕
     */
    public function 💕($💕 = '💕'){
        return '💕';
    }


}
