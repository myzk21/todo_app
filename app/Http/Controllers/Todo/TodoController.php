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
use Illuminate\Pagination\LengthAwarePaginator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = Auth::id();

        $all_todos = Todo::query()
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->get();
        //メインに表示
        //期日なしー＞未完了＋完了したのが今日
        //期日ありー＞未完了＋期日が今日＋完了したのが今日+期限切れ
        $today_todos = $all_todos->filter(function ($todo) {
            $dueDate = Carbon::parse($todo->due);
            $completedDate = Carbon::parse($todo->when_completed);
            return (is_null($todo->when_completed) && $dueDate->isToday()) || //未完了で期日が今日
                    ($completedDate->isToday() && is_null($todo->due)) || //今日完了したが期日がない
                    (is_null($todo->when_completed) && is_null($todo->due)) || //期日なしかつ未完了
                    (is_null($todo->when_completed) && $dueDate->isPast()) || //未完了で遅れたタスク
                    ($dueDate->isPast() && $completedDate->isToday()) || //遅れたタスク＋今日完了したもの
                    ($dueDate->isToday() && $completedDate->isToday()); //期日が今日かつ今日完了
        });
        //期日が今日以外のTodo
        //期日あり＋未完了で期日が今日以外＋期日今日以外で完了したのが今日
        $not_today_todos = $all_todos->filter(function ($todo) {
            $dueDate = Carbon::parse($todo->due);
            $completedDate = Carbon::parse($todo->when_completed);
            return
            $dueDate->isFuture() && //期日が今日以降
            (is_null($todo->when_completed) || $completedDate->isToday());//期日が今日以降かつ未完了or今日完了した
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
