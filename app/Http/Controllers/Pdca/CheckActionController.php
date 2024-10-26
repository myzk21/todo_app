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
                $weeklyCheck->review = (int) $posts['weekly_check_rating'];
                $weeklyCheck->description = $posts['weekly_check_description'];
                $weeklyCheck->save();
                $weeklyAction = new WeeklyAction();
                $weeklyAction->weekly_goal_id = $weeklyGoalId;
                $weeklyAction->description = $posts['weekly_action_description'];
                $weeklyAction->save();
                $activeTab = 'weekly';
            } elseif($monthlyGoalId) {//月間の振り返り
                $monthlyCheck = new MonthlyCheck();
                $monthlyCheck->monthly_goal_id = $monthlyGoalId;
                $monthlyCheck->review = $posts['monthly_check_rating'];
                $monthlyCheck->description = $posts['monthly_check_description'];
                $monthlyCheck->save();

                $monthlyAction = new MonthlyAction();
                $monthlyAction->monthly_goal_id = $monthlyGoalId;
                $monthlyAction->description = $posts['monthly_action_description'];
                $monthlyAction->save();
                $activeTab = 'monthly';
            }
            DB::commit();
            return redirect()->back()->with('activeTab', $activeTab ?? 'weekly');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'データの保存に失敗しました'])->with('activeTab', $activeTab ?? 'weekly');
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
                $weeklyCheck->review = (int) $posts['weekly_check_rating'];
                $weeklyCheck->description = $posts['weekly_check_description'];
                $weeklyCheck->save();
                $weeklyAction = WeeklyAction::find($action_id);
                $weeklyAction->description = $posts['weekly_action_description'];
                $weeklyAction->save();
                $activeTab = 'weekly';
            } elseif($monthlyGoalId) {//月間の振り返り
                $monthlyCheck = MonthlyCheck::find($check_id);
                $monthlyCheck->review = $posts['monthly_check_rating'];
                $monthlyCheck->description = $posts['monthly_check_description'];
                $monthlyCheck->save();

                $monthlyAction = MonthlyAction::find($action_id);
                $monthlyAction->description = $posts['monthly_action_description'];
                $monthlyAction->save();
                $activeTab = 'monthly';
            }
            DB::commit();
            return redirect()->back()->with('activeTab', $activeTab ?? 'weekly');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'データの保存に失敗しました'])->with('activeTab', $activeTab ?? 'weekly');
        }
    }
}
