<?php
namespace App\Services;

use Google\Client as Google_Client;
use App\Models\GoogleUser;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;
use Google\Service\Calendar;
use Carbon\Carbon;

class CalendarService
{
    protected $client;

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
    }

    public function addEvent($googleUser, $eventDetails)
    {
        //トークンの期限切れをチェックし、必要に応じてリフレッシュ
        $this->client->setAccessToken($googleUser->access_token);
        if ($this->client->isAccessTokenExpired()) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($googleUser->refresh_token);
            if (isset($newAccessToken['error'])) {//エラーが発生した場合、再認証してもらう
                \Log::error('リフレッシュトークンエラー:', $newAccessToken);
                session()->put('invalidRefreshToken', true);
            }
            $googleUser->access_token = $this->client->getAccessToken()['access_token'];
            $googleUser->save(); //新しいトークンを保存
        }

        $service = new Google_Service_Calendar($this->client);

        $event = new Google_Service_Calendar_Event(array(
            'summary' => $eventDetails['summary'],
            'start' => array(
                'date' => Carbon::parse($eventDetails['start'])->format('Y-m-d'),
            ),
            'end' => array(
                'date' => Carbon::parse($eventDetails['end'])->format('Y-m-d'),
            ),
        ));

        $calendarId = 'primary'; //プライマリカレンダーを使用
        $event = $service->events->insert($calendarId, $event);

        return $event;
    }

    public function updateEvent($googleUser, $eventId, $eventDetails)
    {
        $this->client->setAccessToken($googleUser->access_token);
        // トークンの期限切れをチェックし、必要に応じてリフレッシュ
        if ($this->client->isAccessTokenExpired()) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($googleUser->refresh_token);
            if (isset($newAccessToken['error'])) {//エラーが発生した場合、再認証してもらう
                \Log::error('リフレッシュトークンエラー:', $newAccessToken);
                session()->put('invalidRefreshToken', true);
            }
            $googleUser->access_token = $this->client->getAccessToken()['access_token'];
            $googleUser->save(); // 新しいトークンを保存
        }

        $service = new Google_Service_Calendar($this->client);

        // 既存のイベントを取得
        $event = $service->events->get('primary', $eventId);

        // イベントの詳細を更新
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $eventDetails['summary'],
            'start' => array(
                'date' => Carbon::parse($eventDetails['start'])->format('Y-m-d'),
            ),
            'end' => array(
                'date' => Carbon::parse($eventDetails['end'])->format('Y-m-d'),
            ),
        ));
        // イベントを更新
        $updatedEvent = $service->events->update('primary', $eventId, $event);

        return $updatedEvent;
    }

    public function deleteEvent($googleUser, $eventId)
    {
        $this->client->setAccessToken($googleUser->access_token);
        // トークンの期限切れをチェックし、必要に応じてリフレッシュ
        if ($this->client->isAccessTokenExpired()) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($googleUser->refresh_token);
            if (isset($newAccessToken['error'])) {//エラーが発生した場合、再認証してもらう
                \Log::error('リフレッシュトークンエラー:', $newAccessToken);
                session()->put('invalidRefreshToken', true);
            }
            $googleUser->access_token = $this->client->getAccessToken()['access_token'];
            $googleUser->save(); // 新しいトークンを保存
        }
        $service = new Google_Service_Calendar($this->client);
        $deleteEvent = $service->events->delete('primary', $eventId);
        return $deleteEvent;
    }
}
