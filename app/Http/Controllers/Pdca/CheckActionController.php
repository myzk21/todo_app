<?php

namespace App\Http\Controllers\Pdca;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Pdca\CheckActionRequest;

class CheckActionController extends Controller
{
    public function store(CheckActionRequest $request) {
        $posts = $request->validated();
        try {
            if($posts['weeklyGoal_id']) { //週間目標の振り返り保存
                $weeklyCheck = new WeeklyCheck();
                $weeklyCheck->weekly_goal_id = $posts['weeklyGoal_id'];
                $weeklyCheck->review = $posts['check-rating'];
                $weeklyCheck->description = $posts['check-description'];
                $weeklyCheck->save();

                $weeklyAction = new WeeklyAction();
                $weeklyAction->weekly_goal_id = $posts['weeklyGoal_id'];
                $weeklyAction->weekly_check_id = $weeklyCheck->id;
                $weeklyAction->description = $posts['action-description'];
                $weeklyAction->save();
            } elseif($posts['monthlyGoal_id']) {//月間の振り返り
                $monthlyCheck = new MonthlyCheck();
                $monthlyCheck->monthly_goal_id = $posts['monthlyGoal_id'];
                $monthlyCheck->review = $posts['check-rating'];
                $monthlyCheck->description = $posts['check-description'];
                $monthlyCheck->save();

                $monthlyAction = new MonthlyAction();
                $monthlyAction->monthly_goal_id = $posts['monthlyGoal_id'];
                $monthlyAction->monthly_check_id = $monthlyCheck->id;
                $monthlyAction->description = $posts['action-description'];
                $monthlyAction->save();
            }
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'データの保存に失敗しました');
        }
    }
}
