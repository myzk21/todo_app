<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TodoStoreRequest;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $posts = $request->all();
        $todo = new Todo();
        $todo->user_id = Auth::id();
        $todo->title = $posts['title'];
        $todo->description = $posts['description'];
        $todo->due = $posts['due'];
        $todo->is_completed = false;
        $todo->progress_rate = $posts['progress_rate'];
        $todo->priority = $posts['priority'];
        // $todo->label = $posts['label'];
        $todo->save();
        // dd($todo);

        return redirect()->route('home')->with('success', 'Todoが追加されました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
