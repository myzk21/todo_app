<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TodoStoreRequest;
use App\Http\Requests\TodoUpdateRequest;
use Carbon\Carbon;
use App\Models\WeeklyGoal;
use App\Models\MonthlyGoal;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::id();

        $all_todos = Todo::query()
        ->where('user_id', $user_id)
        // ->where(function ($query) {
        //     $query->where(function ($subQuery) {//期日が今日でまだ完了していないタスク
        //         $subQuery->whereDate('due', Carbon::today())
        //                  ->whereNull('when_completed');
        //     })
        //     ->orWhere(function ($subQuery) {//期日はないが作成されたのが今日のタスク
        //         $subQuery->whereNull('due')
        //                  ->whereDate('created_at', Carbon::today())
        //                  ->whereNull('when_completed');
        //     })
        //     ->orWhere(function ($subQuery) {//今日完了したタスク
        //         $subQuery->whereNotNull('when_completed')
        //                  ->whereDate('when_completed', Carbon::today());
        //     })
        //     ->orWhere(function ($subQuery) {//期日が今日以外でまだ完了していないタスク
        //         $subQuery->whereDate('due', '!=', Carbon::today())
        //                   ->whereNull('when_completed');
        //     })
        //     ->orWhere(function ($subQuery) {//期日はないが今日以外に作成された未完了のタスク
        //         $subQuery->whereNull('due')
        //                   ->whereDate('created_at', '!=', Carbon::today())
        //                   ->whereNull('when_completed');
        //     });
        // })
        ->orderBy('created_at', 'desc')
        ->get();

            //メインに表示
            //期日なしー＞未完了＋完了したのが今日
            //期日ありー＞未完了で期日が今日＋完了したのが今日
            $today_todos = $all_todos->filter(function ($todo) {
                return (is_null($todo->when_completed) && Carbon::parse($todo->due)->isToday()) || //未完了で期日が今日
                       (Carbon::parse($todo->when_completed)->isToday() && is_null($todo->due)) || //今日完了したが期日がない
                       (is_null($todo->when_completed) && is_null($todo->due)) || //期日なしかつ未完了
                       (Carbon::parse($todo->due)->isToday() && Carbon::parse($todo->when_completed)->isToday()); //期日が今日かつ完了したのも今日
            });
            //期日が今日以外のTodo
            //期日あり＋未完了で期日が今日以外＋期日今日以外で完了したのが今日
            $not_today_todos = $all_todos->filter(function ($todo) {
                return
                !Carbon::parse($todo->due)->isToday() && //期日が今日の日付でない
                (is_null($todo->when_completed) || Carbon::parse($todo->when_completed)->isToday());//期日が今日以外かつ未完了or今日完了した
            })->sortBy(function ($todo) {
                return $todo->due; // 期日で並べ替え
            });

            $weeklyGoal = WeeklyGoal::query()
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();
            $monthlyGoal = MonthlyGoal::query()
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

            return view('todo.list', compact('today_todos', 'not_today_todos', 'weeklyGoal', 'monthlyGoal'));



//ここから下がもとのコード
        // $baseQuery = Todo::query()->where('user_id', $user_id);
        // $today_todos = $baseQuery->where(function ($query) {
        //     //期日が今日かつwhen_completedがnull
        //         $query->whereDate('due', Carbon::today())
        //             ->whereNull('when_completed');
        //     })
        //     ->orWhere(function ($query) {
        //         //期日がnullかつcreated_atが今日かつwhen_completedがnull
        //         $query->whereNull('due')
        //             ->whereDate('created_at', Carbon::today())
        //             ->whereNull('when_completed');
        //     })
        //     ->get();

        // // $completed_todos = $baseQuery->where('when_completed', Carbon::today())->get();
        // $completed_todos = $baseQuery//今日完了したタスク
        //     ->whereNotNull('when_completed')
        //     ->whereDate('when_completed', Carbon::today())
        //     ->get();
        //     dd($completed_todos);

        // $not_today_todos = $baseQuery->where(function ($query) {
        //     $query->where(function ($subQuery) {
        //         $subQuery->whereDate('due', '!=', Carbon::today())//dueが今日以外
        //                   ->whereNull('when_completed'); //かつwhen_completedがnull
        //     })
        //     ->orWhere(function ($subQuery) {
        //         $subQuery->whereNull('due') //かつdueがnull
        //                   ->whereDate('created_at', '!=', Carbon::today()) //created_atが今日以外
        //                   ->whereNull('when_completed'); //かつwhen_completedがnull
        //     });
        // })->get();

        // return view('list', compact('today_todos', 'completed_todos', 'not_today_todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TodoStoreRequest $request)
    {
        $posts = $request->validated();
        $todo = new Todo();
        $todo->user_id = Auth::id();
        $todo->title = $posts['title'];
        $todo->description = $posts['description'];
        $todo->due = $posts['due'];
        $todo->when_completed = null;
        $todo->progress_rate = $posts['progress_rate'];
        $todo->priority = $posts['priority'];
        // $todo->label = $posts['label'];
        $todo->save();

        // return redirect()->route('home')->with('success', 'Todoが追加されました。');
        return response()->json([
            'success' => true,
            'message' => 'Todoが追加されました。',
            'todo' => $todo
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user_id = Auth::id();
        $todo = Todo::query()
            ->where('user_id', $user_id)
            ->where('id', $id)
            ->first();
        return response()->json([
            'success' => true,
            'todo' => $todo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TodoUpdateRequest $request, string $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->title = $request->input('updateTitle');
        $todo->description = $request->input('updateDescription');
        $todo->due = $request->input('updateDue');
        $todo->when_completed = $request->input('when_completed');
        $todo->progress_rate = $request->input('updateProgress_rate');
        $todo->priority = $request->input('updatePriority');
        $todo->save();

        return response()->json([
            'success' => true,
            'message' => 'Todoが更新されました。',
            'todo' => $todo
        ]);
    }

    public function changeTodoStatus(string $id)
    {
        $todo = Todo::findOrFail($id);
        if($todo->when_completed) {
            $todo->when_completed = null;
        } else {
            $todo->when_completed = Carbon::today();
        }
        $todo->save();
        return response()->json([
            'success' => true,
            'message' => 'Todoが更新されました。',
            'todo' => $todo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user_id = Auth::id();
        $todo = Todo::query()
            ->where('user_id', $user_id)
            ->where('id', $id)
            ->first();
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todoが見つかりませんでした'
            ], 404);
        }
        $todo->delete();
        return response()->json([
            'success' => true,
            'todo' => $todo
        ]);
    }
}
