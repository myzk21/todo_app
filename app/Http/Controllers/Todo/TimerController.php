<?php

namespace App\Http\Controllers\Todo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TodoTimer;
use App\Enums\TodoTimerStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimerController extends Controller
{
    public function storeTimerData(Request $request)
    {
        try {
            DB::beginTransaction();

            $user_id = Auth::id();
            $inputs = $request->all();
            $todoId = $inputs['todo_id'];
            $status = $inputs['status'];
            $timer = TodoTimer::where('user_id', $user_id)->where('todo_id', $todoId)->first();

            switch ($status) {
                case 'start':
                    $timer = $this->storeStartTime($user_id, $todoId, $timer);
                    break;
                case 'stop':
                    $timer = $this->storeStopTime($timer);
                    break;
                case 'finish':
                    $timer = $this->storeFinishStatus($timer);
                    break;
                default:
                    throw new \Exception('ステータス不足によるエラーが発生しました');
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => '記録に成功しました',
                'data' => $timer,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '記録に失敗しました',
                'data' => null,
            ]);

        }
    }

    private function storeStartTime($user_id, $todoId, $timer)
    {
        if (!$timer) {
            $timer = TodoTimer::create([
                'user_id' => $user_id,
                'todo_id' => $todoId,
                'started_at' => now(),
                'elapsed_time_at_stop' => 0,
                'status' => TodoTimerStatus::Start->value,
            ]);
        } else {
            $timer->update([
                'started_at' => now(),
                'status' => TodoTimerStatus::Start->value,
            ]);
        }
        return $timer;
    }

     private function storeStopTime($timer)
    {
        if(!$timer) {
                throw new \Exception('記録が見つかりませんでした');
        } else {
            $startTime = Carbon::parse($timer->started_at);
            $now = now();
            ///Unixタイムスタンプで計算することで小数点以下が非表示になる
            $startTimestamp = $startTime->getTimestamp();
            $nowTimestamp = $now->getTimestamp();
            $elapsedTime = $nowTimestamp - $startTimestamp;
            $totalElapsedTime = $elapsedTime + $timer->elapsed_time_at_stop;

            $timer->update([
                'elapsed_time_at_stop' => $totalElapsedTime,
                'status' => TodoTimerStatus::Stop->value,
            ]);
        }
        return $timer;
    }

    private function storeFinishStatus($timer)
    {
        if(!$timer) {
            throw new \Exception('記録が見つかりませんでした');
        } else {
            $timer->update([
                'status' => TodoTimerStatus::Finish->value,
            ]);
        }
        return $timer;
    }
}
