<?php


namespace App\Http\Controllers;

use DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class NotificationsController extends BaseController
{

	private $apiKey = 'MjdjYWM5NDQtOTk4Zi00Zjk1LTkxYjEtMDMzMzE4ZTVlMjg3';

	public function __construct()
	{

	}

	/**
	 * Send notification to users via push notification
	 * @param  Request $request Illuminate object
	 * @return json
	 */
	public function sendNotificationMessage(Request $request)
	{
		$requestData = $request->all();
		$message = array('en' => $requestData['message']);
		$result = $this->sendNotification($message);
		
		$dataforImport = json_decode($result);

		DB::table('notifications')->insert(
			['notification_id' => $dataforImport->id, 'message' => $message['en'], 'recipients' => $dataforImport->recipients, 'created_at' => DB::raw('NOW()')]
		);

		return response()->json(['status' => 200, 'response' => 'Push notificatie verstuurd.']);
	}

	/**
	 * Make API call to OneSignal for push notification
	 * @param  [type] $message array
	 * @return json
	 */
	public function sendNotification($message = NULL)
	{
		$fields = array(
			'app_id' => "d6222836-c73e-4d51-80e3-aa5c27903ec4",
			'included_segments' => array('All'),
			'priority' => 10,
			'contents' => $message
		);
		
		$fields = json_encode($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic ' . $this->apiKey));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
}

?>