<?php

namespace App\Console\Commands;

use App\Mail\DailyTweetCount;
use App\Models\User;
use App\Services\TweetService;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Console\Command;

class DailyTweetCountMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send_daily_tweet_count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count tweet added previous day and send e-mail.';

    private TweetService $tweetService;
    private Mailer $mailer;

    public function __construct(TweetService $tweetService, Mailer $mailer)
    {
        parent::__construct();
        $this->tweetService = $tweetService;
        $this->mailer = $mailer;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tweetCount = $this->tweetService->countYesterdayTweets();
        $users = User::get();

        foreach ($users as $user) {
            $this->mailer->to($user->email)->send(new DailyTweetCount($user, $tweetCount));
        }
        return 0;
    }
}
