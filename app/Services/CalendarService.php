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

    public function setGoogleAccessToken($googleUser) {
        //isAccessTokenExpired()が標準時基準でチェックされるため、UTCの時間で保存
        $expires_in = \Carbon\Carbon::parse($googleUser->expires)->setTimezone('UTC')->timestamp - now('UTC')->timestamp;//Googleユーザ―のアクセストークンの有効期限から今の時間を引くー＞後何秒か求める。これがupdated_atと足されてisAccessTokenExpiredが見極められる
        $this->client->setAccessToken([
            'access_token' => $googleUser->access_token,
            'expires_in' => $expires_in,
            'created' => $googleUser->updated_at->setTimezone('UTC')->timestamp,
            'refresh_token' => $googleUser->refresh_token,
        ]);
    }

    public function addEvent($googleUser, $eventDetails)
    {
        $this->setGoogleAccessToken($googleUser);

        //トークンの期限切れをチェックし、必要に応じてリフレッシュ
        if ($this->client->isAccessTokenExpired()) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($googleUser->refresh_token);
            if (isset($newAccessToken['error'])) {
                \Log::error('リフレッシュトークンエラー:', $newAccessToken);
                session()->put('invalidRefreshToken', true);
                throw new \Exception('Google認証に失敗しました。Googleアカウントに再接続してください。');
            }
            $googleUser->access_token = $newAccessToken['access_token'];
            $expiresIn = $newAccessToken['expires_in'] ?? 3600;
            $googleUser->expires = now()->addSeconds($expiresIn);
            $googleUser->save(); //新しいトークンを保存
            $googleUser->refresh();

            $this->setGoogleAccessToken($googleUser);//$this->clientに最新の情報をセットするための処理
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
        $this->setGoogleAccessToken($googleUser);

        // トークンの期限切れをチェックし、必要に応じてリフレッシュ
        if ($this->client->isAccessTokenExpired()) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($googleUser->refresh_token);
            if (isset($newAccessToken['error'])) {//エラーが発生した場合、再認証してもらう
                \Log::error('リフレッシュトークンエラー:', $newAccessToken);
                session()->put('invalidRefreshToken', true);
                throw new \Exception('Google認証に失敗しました。Googleアカウントに再接続してください。');
            }
            $googleUser->access_token = $newAccessToken['access_token'];
            $expiresIn = $newAccessToken['expires_in'] ?? 3600;
            $googleUser->expires = now()->addSeconds($expiresIn);
            $googleUser->save(); //新しいトークンを保存
            $googleUser->refresh();

            $this->setGoogleAccessToken($googleUser);
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
        $this->setGoogleAccessToken($googleUser);

        // トークンの期限切れをチェックし、必要に応じてリフレッシュ
        if ($this->client->isAccessTokenExpired()) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($googleUser->refresh_token);
            if (isset($newAccessToken['error'])) {//エラーが発生した場合、再認証してもらう
                \Log::error('リフレッシュトークンエラー:', $newAccessToken);
                session()->put('invalidRefreshToken', true);
                throw new \Exception('Google認証に失敗しました。Googleアカウントに再接続してください。');
            }
            $googleUser->access_token = $newAccessToken['access_token'];
            $expiresIn = $newAccessToken['expires_in'] ?? 3600;
            $googleUser->expires = now()->addSeconds($expiresIn);
            $googleUser->save(); //新しいトークンを保存
            $googleUser->refresh();

            $this->setGoogleAccessToken($googleUser);
        }
        $service = new Google_Service_Calendar($this->client);
        $deleteEvent = $service->events->delete('primary', $eventId);
        return $deleteEvent;
    }
}
