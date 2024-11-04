<?php

namespace App\Http\Controllers\Pdca;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Pdca\CreateFirstGoalRequest;
use App\Models\WeeklyGoal;
use App\Models\MonthlyGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PdcaController extends Controller
{
    public function index(Request $request) {
        $user_id = Auth::id();
        $weeklyGoal = WeeklyGoal::query()
        ->where('user_id', $user_id)
        ->with('weeklyCheck', 'weeklyAction')
        ->orderBy('created_at', 'desc')
        ->first();
        $monthlyGoal = MonthlyGoal::query()
        ->where('user_id', $user_id)
        ->with('monthlyCheck', 'monthlyAction')
        ->orderBy('created_at', 'desc')
        ->first();
        return view('pdca.home', compact('weeklyGoal', 'monthlyGoal'));
    }

    public function storeFirstGoal(CreateFirstGoalRequest $request) {//初回の目標登録
        try {
            DB::beginTransaction();
            $posts = $request->validated();
            $weeklyGoal = new WeeklyGoal();
            $weeklyGoal->user_id = Auth::id();
            $weeklyGoal->title = $posts['weekly-goal'];
            $weeklyGoal->due= Carbon::now()->endOfWeek(Carbon::SUNDAY);//今週末
            $weeklyGoal->save();

            $monthlyGoal = new MonthlyGoal();
            $monthlyGoal->user_id = Auth::id();
            $monthlyGoal->title = $posts['monthly-goal'];
            $monthlyGoal->due= Carbon::now()->endOfMonth();//今月末
            $monthlyGoal->save();

            DB::commit();
            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'データの保存に失敗しました']);
            // dd($e->getMessage());
        }
    }

}
