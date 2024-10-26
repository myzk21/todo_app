<?php

namespace App\Http\Controllers\Pdca;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WeeklyGoal;
use App\Models\MonthlyGoal;
use App\Http\Requests\Pdca\GoalRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function store(GoalRequest $request) {
        $posts = $request->validated();
        try {
            DB::beginTransaction();
            $posts = $request->validated();
            if($request->input('weekly_goal')) {
                $weeklyGoal = new WeeklyGoal();
                $weeklyGoal->user_id = Auth::id();
                $weeklyGoal->title = $posts['weekly_goal'];
                $weeklyGoal->due = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                $weeklyGoal->save();
            } elseif($request->input('monthly_goal')) {
                $monthlyGoal = new MonthlyGoal();
                $monthlyGoal->user_id = Auth::id();
                $monthlyGoal->title = $posts['monthly_goal'];
                $monthlyGoal->due= Carbon::now()->endOfMonth();//今月末
                $monthlyGoal->save();
            }
            DB::commit();
            return redirect()->route('home');
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error', 'データの保存に失敗しました');
        }
    }
}
