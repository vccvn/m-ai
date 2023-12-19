<?php

namespace App\Console\Commands;

use App\Repositories\MBTI\DetailRepository;
use App\Repositories\MBTI\MatchRepository;
use Illuminate\Console\Command;

class AddMBTI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mbti:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DetailRepository $detailRepository, MatchRepository $matchRepository)
    {
        if ($matchRepository->first()) {
            $detailRepository->query()->delete();
            $matchRepository->query()->delete();
            echo "delete\n";
            return Command::SUCCESS;
        }
        $mbtiList = ["INFP", "ENFP", "INFJ", "ENFJ", "INTJ", "ENTJ", "INTP", "ENTP", "ISFP", "ESFP", "ISTP", "ESTP", "ISFJ", "ESFJ", "ISTJ", "ESTJ"];
        $detailMap = [];
        $matching = [[["INFP", "INFP", 4], ["INFP", "ENFP", 4], ["INFP", "INFJ", 5], ["INFP", "ENFJ", 5], ["INFP", "INTJ", 4], ["INFP", "ENTJ", 5], ["INFP", "INTP", 4], ["INFP", "ENTP", 4], ["INFP", "ISFP", 1], ["INFP", "ESFP", 1], ["INFP", "ISTP", 1], ["INFP", "ESTP", 1], ["INFP", "ISFJ", 2], ["INFP", "ESFJ", 2], ["INFP", "ISTJ", 2], ["INFP", "ESTJ", 2]], [["ENFP", "INFP", 4], ["ENFP", "ENFP", 4], ["ENFP", "INFJ", 5], ["ENFP", "ENFJ", 4], ["ENFP", "INTJ", 5], ["ENFP", "ENTJ", 4], ["ENFP", "INTP", 4], ["ENFP", "ENTP", 4], ["ENFP", "ISFP", 1], ["ENFP", "ESFP", 1], ["ENFP", "ISTP", 1], ["ENFP", "ESTP", 1], ["ENFP", "ISFJ", 2], ["ENFP", "ESFJ", 2], ["ENFP", "ISTJ", 2], ["ENFP", "ESTJ", 2]], [["INFJ", "INFP", 5], ["INFJ", "ENFP", 5], ["INFJ", "INFJ", 4], ["INFJ", "ENFJ", 4], ["INFJ", "INTJ", 4], ["INFJ", "ENTJ", 4], ["INFJ", "INTP", 5], ["INFJ", "ENTP", 5], ["INFJ", "ISFP", 2], ["INFJ", "ESFP", 2], ["INFJ", "ISTP", 1], ["INFJ", "ESTP", 1], ["INFJ", "ISFJ", 2], ["INFJ", "ESFJ", 2], ["INFJ", "ISTJ", 2], ["INFJ", "ESTJ", 2]], [["ENFJ", "INFP", 5], ["ENFJ", "ENFP", 4], ["ENFJ", "INFJ", 4], ["ENFJ", "ENFJ", 4], ["ENFJ", "INTJ", 4], ["ENFJ", "ENTJ", 4], ["ENFJ", "INTP", 4], ["ENFJ", "ENTP", 4], ["ENFJ", "ISFP", 5], ["ENFJ", "ESFP", 1], ["ENFJ", "ISTP", 1], ["ENFJ", "ESTP", 1], ["ENFJ", "ISFJ", 2], ["ENFJ", "ESFJ", 2], ["ENFJ", "ISTJ", 2], ["ENFJ", "ESTJ", 2]], [["INTJ", "INFP", 4], ["INTJ", "ENFP", 5], ["INTJ", "INFJ", 4], ["INTJ", "ENFJ", 4], ["INTJ", "INTJ", 4], ["INTJ", "ENTJ", 4], ["INTJ", "INTP", 5], ["INTJ", "ENTP", 5], ["INTJ", "ISFP", 3], ["INTJ", "ESFP", 3], ["INTJ", "ISTP", 3], ["INTJ", "ESTP", 3], ["INTJ", "ISFJ", 2], ["INTJ", "ESFJ", 2], ["INTJ", "ISTJ", 2], ["INTJ", "ESTJ", 2]], [["ENTJ", "INFP", 5], ["ENTJ", "ENFP", 4], ["ENTJ", "INFJ", 4], ["ENTJ", "ENFJ", 4], ["ENTJ", "INTJ", 4], ["ENTJ", "ENTJ", 4], ["ENTJ", "INTP", 5], ["ENTJ", "ENTP", 4], ["ENTJ", "ISFP", 3], ["ENTJ", "ESFP", 3], ["ENTJ", "ISTP", 3], ["ENTJ", "ESTP", 3], ["ENTJ", "ISFJ", 3], ["ENTJ", "ESFJ", 3], ["ENTJ", "ISTJ", 3], ["ENTJ", "ESTJ", 3]], [["INTP", "INFP", 4], ["INTP", "ENFP", 4], ["INTP", "INFJ", 5], ["INTP", "ENFJ", 4], ["INTP", "INTJ", 5], ["INTP", "ENTJ", 5], ["INTP", "INTP", 4], ["INTP", "ENTP", 4], ["INTP", "ISFP", 3], ["INTP", "ESFP", 3], ["INTP", "ISTP", 3], ["INTP", "ESTP", 3], ["INTP", "ISFJ", 2], ["INTP", "ESFJ", 2], ["INTP", "ISTJ", 2], ["INTP", "ESTJ", 5]], [["ENTP", "INFP", 4], ["ENTP", "ENFP", 4], ["ENTP", "INFJ", 5], ["ENTP", "ENFJ", 4], ["ENTP", "INTJ", 5], ["ENTP", "ENTJ", 4], ["ENTP", "INTP", 4], ["ENTP", "ENTP", 4], ["ENTP", "ISFP", 3], ["ENTP", "ESFP", 3], ["ENTP", "ISTP", 3], ["ENTP", "ESTP", 3], ["ENTP", "ISFJ", 2], ["ENTP", "ESFJ", 2], ["ENTP", "ISTJ", 2], ["ENTP", "ESTJ", 2]], [["ISFP", "INFP", 1], ["ISFP", "ENFP", 1], ["ISFP", "INFJ", 2], ["ISFP", "ENFJ", 5], ["ISFP", "INTJ", 3], ["ISFP", "ENTJ", 3], ["ISFP", "INTP", 3], ["ISFP", "ENTP", 3], ["ISFP", "ISFP", 2], ["ISFP", "ESFP", 2], ["ISFP", "ISTP", 2], ["ISFP", "ESTP", 2], ["ISFP", "ISFJ", 3], ["ISFP", "ESFJ", 5], ["ISFP", "ISTJ", 3], ["ISFP", "ESTJ", 5]], [["ESFP", "INFP", 1], ["ESFP", "ENFP", 1], ["ESFP", "INFJ", 2], ["ESFP", "ENFJ", 1], ["ESFP", "INTJ", 3], ["ESFP", "ENTJ", 3], ["ESFP", "INTP", 3], ["ESFP", "ENTP", 3], ["ESFP", "ISFP", 2], ["ESFP", "ESFP", 2], ["ESFP", "ISTP", 2], ["ESFP", "ESTP", 2], ["ESFP", "ISFJ", 5], ["ESFP", "ESFJ", 3], ["ESFP", "ISTJ", 5], ["ESFP", "ESTJ", 3]], [["ISTP", "INFP", 1], ["ISTP", "ENFP", 1], ["ISTP", "INFJ", 1], ["ISTP", "ENFJ", 1], ["ISTP", "INTJ", 3], ["ISTP", "ENTJ", 3], ["ISTP", "INTP", 3], ["ISTP", "ENTP", 3], ["ISTP", "ISFP", 2], ["ISTP", "ESFP", 2], ["ISTP", "ISTP", 2], ["ISTP", "ESTP", 2], ["ISTP", "ISFJ", 3], ["ISTP", "ESFJ", 5], ["ISTP", "ISTJ", 3], ["ISTP", "ESTJ", 5]], [["ESTP", "INFP", 1], ["ESTP", "ENFP", 1], ["ESTP", "INFJ", 1], ["ESTP", "ENFJ", 1], ["ESTP", "INTJ", 3], ["ESTP", "ENTJ", 3], ["ESTP", "INTP", 3], ["ESTP", "ENTP", 3], ["ESTP", "ISFP", 2], ["ESTP", "ESFP", 2], ["ESTP", "ISTP", 2], ["ESTP", "ESTP", 2], ["ESTP", "ISFJ", 5], ["ESTP", "ESFJ", 3], ["ESTP", "ISTJ", 5], ["ESTP", "ESTJ", 3]], [["ISFJ", "INFP", 2], ["ISFJ", "ENFP", 2], ["ISFJ", "INFJ", 2], ["ISFJ", "ENFJ", 2], ["ISFJ", "INTJ", 2], ["ISFJ", "ENTJ", 3], ["ISFJ", "INTP", 2], ["ISFJ", "ENTP", 2], ["ISFJ", "ISFP", 3], ["ISFJ", "ESFP", 5], ["ISFJ", "ISTP", 3], ["ISFJ", "ESTP", 5], ["ISFJ", "ISFJ", 4], ["ISFJ", "ESFJ", 4], ["ISFJ", "ISTJ", 4], ["ISFJ", "ESTJ", 4]], [["ESFJ", "INFP", 2], ["ESFJ", "ENFP", 2], ["ESFJ", "INFJ", 2], ["ESFJ", "ENFJ", 2], ["ESFJ", "INTJ", 2], ["ESFJ", "ENTJ", 3], ["ESFJ", "INTP", 2], ["ESFJ", "ENTP", 2], ["ESFJ", "ISFP", 5], ["ESFJ", "ESFP", 3], ["ESFJ", "ISTP", 5], ["ESFJ", "ESTP", 3], ["ESFJ", "ISFJ", 4], ["ESFJ", "ESFJ", 4], ["ESFJ", "ISTJ", 4], ["ESFJ", "ESTJ", 4]], [["ISTJ", "INFP", 2], ["ISTJ", "ENFP", 2], ["ISTJ", "INFJ", 2], ["ISTJ", "ENFJ", 2], ["ISTJ", "INTJ", 2], ["ISTJ", "ENTJ", 3], ["ISTJ", "INTP", 2], ["ISTJ", "ENTP", 2], ["ISTJ", "ISFP", 3], ["ISTJ", "ESFP", 5], ["ISTJ", "ISTP", 3], ["ISTJ", "ESTP", 5], ["ISTJ", "ISFJ", 4], ["ISTJ", "ESFJ", 4], ["ISTJ", "ISTJ", 4], ["ISTJ", "ESTJ", 4]], [["ESTJ", "INFP", 2], ["ESTJ", "ENFP", 2], ["ESTJ", "INFJ", 2], ["ESTJ", "ENFJ", 2], ["ESTJ", "INTJ", 2], ["ESTJ", "ENTJ", 3], ["ESTJ", "INTP", 5], ["ESTJ", "ENTP", 2], ["ESTJ", "ISFP", 5], ["ESTJ", "ESFP", 3], ["ESTJ", "ISTP", 5], ["ESTJ", "ESTP", 3], ["ESTJ", "ISFJ", 4], ["ESTJ", "ESFJ", 4], ["ESTJ", "ISTJ", 4], ["ESTJ", "ESTJ", 4]]];
        $matchingMap = [];
        echo "create MBTI\n";
        foreach ($mbtiList as $key) {
            $mbti = $detailRepository->create(['mbti' => $key]);
            echo "Created $key\n";
            $detailMap[$key] = $mbti->id;
        }

        echo "Merge...\n";
        foreach ($matching as $row) {
            foreach ($row as $column) {
                $matchingMap[] = [
                    'first_mbti' => $column[0],
                    'second_mbti' => $column[1],
                    'score' => $column[2],
                ];
            }
        }
        $results = [];
        foreach ($matchingMap as $data) {
            $results[] = $matchRepository->create($data)->toArray();
            echo "Merged $data[first_mbti] - $data[second_mbti] = $data[score]\n";
        }


        print_r($results);
        return Command::SUCCESS;
    }
}
