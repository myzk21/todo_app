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
use App\Models\GoogleUser;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\CalendarService;
use  App\Http\Controllers\Auth\OAuthController;
use Illuminate\Support\Facades\DB;


class TodoController extends Controller
{
    protected $CalendarService;

    public function __construct(CalendarService $CalendarService)
    {
        $this->CalendarService = $CalendarService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = Auth::id();

        $google_user = GoogleUser::where('user_id', $user_id)->first();
        $keyWord = $request->input('keyWord');

        $incompleteQuery = Todo::query()
            ->where('user_id', $user_id)
            ->where('is_completed', false)
            ->orderBy('created_at', 'desc');
        if(!empty($keyWord)) {
            $incompleteQuery->where('title', 'like', '%' . trim($keyWord) . '%');
        }
        $incompleteTodos = $incompleteQuery->paginate(10)->appends(['keyWord' => $keyWord]);

        $completeQuery = Todo::query()
            ->where('user_id', $user_id)
            ->where('is_completed', true)
            ->orderBy('when_completed', 'desc');

        if(!empty($keyWord)) {
            $completeQuery->where('title', 'like', '%' . trim($keyWord) . '%');
        }
        $completeTodos = $completeQuery->paginate(10)->appends(['keyWord' => $keyWord]);

        $weeklyGoal = WeeklyGoal::query()
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->first();
        $monthlyGoal = MonthlyGoal::query()
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->first();
        return view('todo.list', compact('incompleteTodos', 'completeTodos', 'weeklyGoal', 'monthlyGoal', 'google_user', 'keyWord'));
    }
///以下は非同期でタスク一覧を表示させようと試みた際に作成した。削除しても問題ない
    // public function fetchData(Request $request, $type)
    // {
    //     $user_id = Auth::id();
    //     $keyWord = $request->input('keyWord');
    //     $result = null;
    //     if($type == 'incomplete') {
    //         $incompleteQuery = Todo::query()
    //         ->where('user_id', $user_id)
    //         ->where('is_completed', false)
    //         ->orderBy('created_at', 'desc');
    //         if(!empty($keyWord)) {
    //             $incompleteQuery->where('title', 'like', '%' . trim($keyWord) . '%');
    //         }
    //         $result = $incompleteQuery->paginate(10)->appends(['keyWord' => $keyWord]);
    //     } else if($type == 'complete') {
    //         $completeQuery = Todo::query()
    //         ->where('user_id', $user_id)
    //         ->where('is_completed', true)
    //         ->orderBy('when_completed', 'desc');

    //         if(!empty($keyWord)) {
    //             $completeQuery->where('title', 'like', '%' . trim($keyWord) . '%');
    //         }
    //         $result = $completeQuery->paginate(10)->appends(['keyWord' => $keyWord]);
    //     }
    //     return response()->json($result);
    // }
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
        try {
            DB::beginTransaction();
            $posts = $request->validated();
            if ($posts['due'] !== null && $request->has('addToCalendar')) {
                $google_user_id = $request->input('googleUser');
                $google_user = GoogleUser::where('id', $google_user_id)->first();
                //Googleカレンダーにイベントを追加
                $eventDetails = [
                    'summary' => $posts['title'],
                    'start' => $posts['due'],
                    'end' => $posts['due'],
                ];
                // Google カレンダーにイベントを追加
                $event = $this->CalendarService->addEvent($google_user, $eventDetails);
            }
            $todo = new Todo();
            $todo->user_id = Auth::id();
            $todo->title = $posts['title'];
            $todo->description = $posts['description'];
            $todo->due = $posts['due'];
            $todo->is_completed = false;
            $todo->progress_rate = $posts['progress_rate'];
            $todo->priority = $posts['priority'];
            $todo->event_id = isset($event) ? $event->id : null;
            $todo->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Todoが追加されました。',
                'todo' => $todo
            ]);
        } catch(\Exception $error) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました: ' . $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user_id = Auth::id();
        $todo = Todo::with('user.googleUser')
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
        try {
            DB::beginTransaction();
            $updatePost = $request->validated();
            $todo = Todo::findOrFail($id);
            if ($updatePost['updateDue'] !== null && $request->has('updateToCalendar')) {//期日があるかつチェックされていた場合
                $google_user_id = $request->input('googleUser');
                $google_user = GoogleUser::where('id', $google_user_id)->first();

                if($request->input('event_id')) {//イベントIDがあれば更新処理
                    $event_id = $request->input('event_id');
                    $eventDetails = [
                        'summary' => $updatePost['updateTitle'],
                        'start' => $updatePost['updateDue'],
                        'end' => $updatePost['updateDue'],
                    ];
                    // Google カレンダーにイベントを追加
                    $event = $this->CalendarService->updateEvent($google_user, $event_id, $eventDetails);
                } else {
                    //新規作成
                    $eventDetails = [
                        'summary' => $updatePost['updateTitle'],
                        'start' => $updatePost['updateDue'],
                        'end' => $updatePost['updateDue'],
                    ];
                    // Google カレンダーにイベントを追加
                    $event = $this->CalendarService->addEvent($google_user, $eventDetails);
                }
            } elseif(!$request->has('updateToCalendar') && $todo->event_id) {//チェック外れた状態＋カレンダーに存在
                $google_user_id = $request->input('googleUser');
                $google_user = GoogleUser::where('id', $google_user_id)->first();
                $this->CalendarService->deleteEvent($google_user, $todo->event_id);//カレンダーから削除
            }
            $todo->title = $updatePost['updateTitle'];
            $todo->description = $updatePost['updateDescription'];
            $todo->due = $updatePost['updateDue'];
            $todo->progress_rate = $updatePost['updateProgress_rate'];
            $todo->priority = $updatePost['updatePriority'];
            $todo->event_id = isset($event) ? $event->id : null;
            $todo->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Todoが更新されました。',
                'todo' => $todo
            ]);
        } catch(\Exception $error) {
            DB::rollBack();
            if (isset($event) && $event->id) {//途中でエラーが起きてもカレンダーには追加されてしまうため削除
                $this->CalendarService->deleteEvent($google_user, $event->id);
            }
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました: ' . $error->getMessage(),
            ], 500);
            // dd($error->getMessage());
        }
    }

    public function changeTodoStatus(string $id)
    {
        $todo = Todo::findOrFail($id);
        if($todo->is_completed) {
            $todo->is_completed = false;
            $todo->when_completed = null;
        } else {
            $todo->is_completed = true;
            $todo->when_completed = Carbon::now();
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
        try {
            DB::beginTransaction();
            $user_id = Auth::id();
            $todo = Todo::with('user.googleUser')
                ->where('user_id', $user_id)
                ->where('id', $id)
                ->first();
            if (!$todo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Todoが見つかりませんでした'
                ], 404);
            }

            if($todo->event_id) {//カレンダーに存在する場合
                $google_user = $todo->user->googleUser;
                $this->CalendarService->deleteEvent($google_user, $todo->event_id);
            }
            $todo->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'todo' => $todo
            ]);
        } catch(\Exception $error) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '削除処理に失敗しました: ' . $error->getMessage()
            ], 500);
        }
   }
}
