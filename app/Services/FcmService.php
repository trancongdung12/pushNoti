<?php

			namespace App\Services;

			use GuzzleHttp\Client;
			use GuzzleHttp\Exception\GuzzleException;
			use Illuminate\Http\Response;
			use Illuminate\Support\Facades\Log;
			use App\Contracts\NotificationInterface;
			use Exception;

			class FcmService implements NotificationInterface
			{
				/**
				 * @param $deviceTokens
				 * @param $data
				 * @throws GuzzleException
				 */
				public function sendBatchNotification($deviceTokens, $data)
				{
					self::subscribeTopic($deviceTokens, $data['topicName']);
					self::sendNotification($data, $data['topicName']);
					self::unsubscribeTopic($deviceTokens, $data['topicName']);
				}

				/**
				 * @param $data
				 * @param $topicName
				 * @throws GuzzleException
				 */
				public function sendNotification($data, $topicName)
				{
					$url = 'https://fcm.googleapis.com/fcm/send';
					$data = [
						'to' => '/topics/' . $topicName,
						'notification' => [
							'body' => $data['body'] ?? '',
							'title' => $data['title'] ?? '',
							'image' => $data['image'] ?? null,
						],
						'data' => [
							// Cục data em muốn truyền vào để bên RN nhận
							// Ví dụ:
							'status' => $data['status'] ?? null,
							'type' => $data['type'] ?? null,
							'orderId' => $data['orderId'] ?? null,
						],
						'apns' => [
							'payload' => [
								'aps' => [
									'mutable-content' => 1,
								],
							],
							'fcm_options' => [
								'image' => $data['image'] ?? null,
							], 
						],
					];
					$this->execute($url, $data);
				}

				/**
				 * @param $deviceToken
				 * @param $topicName
				 * @throws GuzzleException
				 */
				public function subscribeTopic($deviceTokens, $topicName)
				{
					$url = 'https://iid.googleapis.com/iid/v1:batchAdd';
					$data = [
						'to' => '/topics/' . $topicName,
						'registration_tokens' => $deviceTokens,
					];

					$this->execute($url, $data);
				}

				/**
				 * @param $deviceToken
				 * @param $topicName
				 * @throws GuzzleException
				 */
				public function unsubscribeTopic($deviceTokens, $topicName)
				{
					$url = 'https://iid.googleapis.com/iid/v1:batchRemove';
					$data = [
						'to' => '/topics/' . $topicName,
						'registration_tokens' => $deviceTokens,
					];

					$this->execute($url, $data);
				}

				/**
				 * @param $url
				 * @param array $dataPost
				 * @param string $method
				 * @return bool
				 * @throws GuzzleException
				 */
				public function execute($url, $dataPost = [], $method = 'POST')
				{
					$result = false;
					try {
						$client = new Client();
						$result = $client->request($method, $url, [
							'headers' => [
								'Content-Type' => 'application/json',
								'Authorization' => 'key=' . env('FCM_KEY'),
							],
							'json' => $dataPost,
							'timeout' => 300,
						]);
						$result = $result->getStatusCode() == Response::HTTP_OK;
					} catch (Exception $e) {
						Log::debug($e);
					}
					return $result;
				}
			}
