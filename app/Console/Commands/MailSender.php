<?php

namespace App\Console\Commands;

use App\Services\Mailers\Mailer;
use Illuminate\Console\Command;

class MailSender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send {email?} {--subject=test} {--message=Test_Message} {--delay=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gui mail test {email?: email muon gui} {--subject=test : tieu de email} {--message=Test_Message : noi dung email} {--delay=0 : Thoi gian delay }';
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $to = $this->argument('email')??'doanln16@gmail.com';
        $subject = $this->option('subject')??'Test email Subject';
        $message = $this->option('message')??'Test email message';
        $delay = $this->option('delay');
        $mail = Mailer::to($to)->subject($subject)->content($message)->body('mails.message');
        if($delay) $mail->sendAfter($delay);
        else $mail->send();

        return Command::SUCCESS;
    }
}
