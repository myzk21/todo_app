<?php

namespace App\Http\Controllers\Pdca;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Pdca\CheckActionRequest;
use App\Models\WeeklyCheck;
use App\Models\WeeklyAction;
use App\Models\MonthlyCheck;
use App\Models\MonthlyAction;
use Illuminate\Support\Facades\DB;

class CheckActionController extends Controller
{
    public function store(CheckActionRequest $request) {
        $posts = $request->validated();
        try {
            DB::beginTransaction();

            $weeklyGoalId = (int) $request->input('weeklyGoal_id');
            $monthlyGoalId = (int) $request->input('monthlyGoal_id');

            if($weeklyGoalId) { //週間目標の振り返り保存
                $weeklyCheck = new WeeklyCheck();
                $weeklyCheck->weekly_goal_id = $weeklyGoalId;
                $weeklyCheck->review = (int) $posts['check-rating'];
                $weeklyCheck->description = $posts['check-description'];
                $weeklyCheck->save();
                $weeklyAction = new WeeklyAction();
                $weeklyAction->weekly_goal_id = $weeklyGoalId;
                $weeklyAction->description = $posts['action-description'];
                $weeklyAction->save();
            } elseif($monthlyGoalId) {//月間の振り返り
                $monthlyCheck = new MonthlyCheck();
                $monthlyCheck->monthly_goal_id = $monthlyGoalId;
                $monthlyCheck->review = $posts['check-rating'];
                $monthlyCheck->description = $posts['check-description'];
                $monthlyCheck->save();

                $monthlyAction = new MonthlyAction();
                $monthlyAction->monthly_goal_id = $monthlyGoalId;
                $monthlyAction->description = $posts['action-description'];
                $monthlyAction->save();
            }
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error', 'データの保存に失敗しました');
        }
    }
    public function update(CheckActionRequest $request, $check_id, $action_id) {
        $posts = $request->validated();
        try {
            DB::beginTransaction();

            $weeklyGoalId = (int) $request->input('weeklyGoal_id');
            $monthlyGoalId = (int) $request->input('monthlyGoal_id');

            if($weeklyGoalId) { //週間目標の振り返り保存
                $weeklyCheck = WeeklyCheck::find($check_id);
                // $weeklyCheck->weekly_goal_id = $weeklyGoalId;
                $weeklyCheck->review = (int) $posts['check-rating'];
                $weeklyCheck->description = $posts['check-description'];
                $weeklyCheck->save();
                $weeklyAction = WeeklyAction::find($action_id);
                // $weeklyAction->weekly_goal_id = $weeklyGoalId;
                $weeklyAction->description = $posts['action-description'];
                $weeklyAction->save();
            } elseif($monthlyGoalId) {//月間の振り返り
                $monthlyCheck = MonthlyCheck::find($check_id);
                // $monthlyCheck->monthly_goal_id = $monthlyGoalId;
                $monthlyCheck->review = $posts['check-rating'];
                $monthlyCheck->description = $posts['check-description'];
                $monthlyCheck->save();

                $monthlyAction = MonthlyAction::find($action_id);
                // $monthlyAction->monthly_goal_id = $monthlyGoalId;
                $monthlyAction->description = $posts['action-description'];
                $monthlyAction->save();
            }
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error', 'データの保存に失敗しました');
        }
    }
}
