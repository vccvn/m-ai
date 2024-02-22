<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\Payments\AgentPaymentLogRepository;
use App\Repositories\Promotions\CampaignRepository;
use App\Services\EKYC\EKYCService;
use App\Services\ESMS\ESMSService;
use App\Services\Reports\ReportService;
use Gomee\Files\Filemanager;
use Illuminate\Console\Command;
use ParsedownExtra;

class TestSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test Hệ thống';
    /**
     * filemanager
     *
     * @var Filemanager
     */
    protected $fileManager = null;
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Test:...");
        try {
            $text = "
Việc stream video trong Laravel không quá khó khăn. Dưới đây là một hướng dẫn cơ bản về cách stream video sử dụng Laravel:

Đầu tiên, bạn sẽ cần tạo một route trong tệp `routes/web.php` hoặc `routes/api.php` của bạn. Route này sẽ có nhiệm vụ trả về dữ liệu stream cho video.

```php
Route::get('stream-video', 'Chuyên gia AI@streamVideo');
```
Tiếp theo, tạo một phương thức `streamVideo()` trong controller cần thiết (`Chuyên gia AI` trong ví dụ này). Phương thức này sẽ cung cấp stream dữ liệu cho video của bạn:

```php
public function streamVideo() {
    \$path = 'đường dẫn đến video của bạn'; // ví dụ: storage_path('app/public/videos/my_video.mp4');
    if (!File::exists(\$path)) {
        abort(404);
    }

    \$video = LaravelVideoStream::create(\$path);

    return Response::stream(
        function () use (\$video) {
            \$video->start();
        }
    );
}
```
Trong đó, `LaravelVideoStream::create()` là hàm được sử dụng để tạo stream video.

Lưu ý: Để sử dụng được `LaravelVideoStream`, bạn cần cài đặt thư viện `mohammad-azimi/laravel-video-stream`.

```bash
composer require mohammad-azimi/laravel-video-stream
```

```html
<p>Test</p>
```

Ví dụ trên đầu ra sẽ là byte stream của video, nó sẽ rất hữu ích khi bạn muốn tạo một ứng dụng streaming video như Netflix hoặc Youtube.

Hãy chắc chắn rằng bạn đã bật mở rộng Fileinfo trong tệp cấu hình `php.ini` của bạn, vì nó cần thiết để kiểm tra loại tệp.

Nếu bạn gặp khó khăn, hãy nhớ rằng có nhiều nguồn trực tuyến, bao gồm cả tài liệu Laravel chính thức và các diễn đàn như StackOverflow hay Github, nơi bạn có thể tìm thấy hỗ trợ chuyên nghiệp và sự giúp đỡ từ cộng đồng Laravel rộng lở hơn.
            ";
$Extra = new ParsedownExtra();
// echo $Extra->text('# Header {.sth}'); # prints: <h1 class="sth">Header</h1>
// $data['message'] = preg_replace('/(<html[^>]>.*<body>|<\/body>|<\/html>)/i', '', $data['content']);
// echo $Extra->text($text);
// die;
            $mapData = [];
            preg_match_all('/\`\`\`([A-z0-9_\-]+)\n([^\`\`\`]*)\n\`\`\`/i', $text, $matches);
            // dd($matches);
                if(count($matches[0])){
                    foreach ($matches[1] as $i => $t) {
                        if(in_array(strtolower($t), ['html', 'xml'])){
                        $mark = '.startMarkdown ' . $i . ' .endMarkdown';
                        $mapData[$i] = [$mark, $matches[2][$i]];
                        $text = str_replace($matches[2][$i], $mark, $text);
                    }
                    }
                }


            return 0;
            /**
             * @var EKYCService
             */
            $eKYC = app(EKYCService::class);
            $eKYC->verifyCICard(storage_path('ekyc/test.jpeg'));
            return ;

            /**
             * @var ESMSService
             */
            $eSMS = app(ESMSService::class);
            if(!$eSMS->sendMessage('0945786960', '234567 la ma xac minh dang ky Baotrixemay cua ban')){
                $this->warn($eSMS->getErrorMessage());
            }else{
                $this->info('Gửi tin nhắn thành công');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
        return 0;
    }

    public function removeHtml($path)
    {
        if ($list = $this->fileManager->getList($path)) {
            foreach ($list as $item) {
                if ($item->type == 'folder') {
                    $this->removeHtml($item->path);
                } elseif ($item->extension == 'html') {
                    $this->fileManager->delete($item->path);
                    echo "delete $item->path\n";
                }
            }
        }
    }
}
