<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Jobs\PushNotificationJob;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function pushNotification () {
        $token = "cDeBQCXmRSG0A1PV-phkQY:APA91bFMozkpGRXyme1TdfBleFnhOQlsZiytUbey611pshtk3J7R73N6FgL52ZBxJqT3gX69iajEoP2jB1zFw5kHwesIRVjq4z5dNh1QR79keCVs8zK9tuSQR-aLp1m0hlHRPJbOEojb";
        PushNotificationJob::dispatch('sendBatchNotification', [
            $token,
            [
                'topicName' => 'play',
                'title' => '123123123123123',
                'body' => 'zzzzzzzzzzzzzzzzzz',
                // Nếu có thêm data tại mục 2 thì truyền thêm tại đây
                // Ví dụ:
                // 'topicName' => 'Your topic name',
                // 'title' => 'Your title',
                // 'body' => 'Your body',
                // 'orderId' => 'Your orderId',
                // 'status' => 'Status',
                // 'type' => 'Your type'
            ],
        ]);
    }
    public function view () {
        return view('welcome');
    }
}
