<?php

namespace App\Console\Commands;

use App\User;
use App\Notifications\WeGotOne;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Notification;

class FindNeses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
     public function handle()
    {
        $html = $this->htmlForSearching();
        $crawler = new Crawler($html);

        $table = $crawler->filterXPath('//table/tr');

        foreach ($table as $domElement) {
            $datums = $domElement->getElementsByTagName('td');
            
            if ($datums->length < 5)
            {
                continue;
            }

            $name = $datums->item(1)->nodeValue;
            $sale = intval($datums->item(2)->nodeValue);
            $available = intval($datums->item(3)->nodeValue);

            if ($sale > 0 || $available || 0){
                $this->foundOne($name);
            } else {
                $this->pulse();
            }
        }
    }

    protected function foundOne($name)
    {
        $user = (new User(['slack_webhook_url' => 'https://hooks.slack.com/services/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']));
        $user->notify(new WeGotOne($name));
    }

    public function pulse()
    {
        $user = (new User(['slack_webhook_url' => 'https://hooks.slack.com/services/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']));
        $user->notify(new StillNothing($name));
    }

    protected function htmlForSearching()
    {
        // Get cURL resource
        $ch = curl_init();

        // Set url
        curl_setopt($ch, CURLOPT_URL, 'http://brickseek.com/target-inventory-checker/?sku=207-29-0180');

        // Set method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        // Set options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Cookie: __cfduid=df65c0f5684caf807bfdbf493b2b7e8431480556155; AWSALB=v4hUpQbtoRJ2WAWZfU+7WN3d2BfKD1JR32+u6DAeVIba4Hlk9egwHJG86uqVkYsf2JU1dV9En1kQaL1rMLdaGATQ0tJ++Dl0Yi5oLISfjHgATbe4VHsVbRxjskPI",
          "Content-Type: application/x-www-form-urlencoded; charset=utf-8",
         ]
        );
        // Create body
        $body = [
          "store_type" => "1",
          "zip" => "15217",
          "sort" => "distance",
          "sku" => "207-29-0180",
          ];
        $body = http_build_query($body);

        // Set body
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // Send the request & save response to $resp
        $resp = curl_exec($ch);

        if(!$resp) {
          die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }

        // Close request to clear up some resources
        curl_close($ch);

        return $resp;


    }
}
