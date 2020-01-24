<?php

namespace App\Http\Controllers;

use App\Task;
use Validator;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    // all todos by category
    public function fetchAll($tab)
    {
        if($tab=='all'){
            return Task::all();
        }elseif($tab=='active'){
            return Task::where('completed', 0)->get();
        }else{
            return Task::where('completed', 1)->get();
        }
    }

    // all todos by category
    public function item()
    {
        $all = Task::all()->count();
        $active = Task::where('completed', 0)->count();
        $complete = Task::where('completed', 1)->count();
        if($active==0 and $complete > 0){
            $checked=1;
        }else{
            $checked=0;
        }
        return array(
            'all'     => $all,
            'active'     => $active,
            'complete'   => $complete,
            'checked'   => $checked
        );
    }

    // add todo
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()],422);
        }

        $todos = new Task([
            'title' => $request->input('title'),
            'completed' => false
        ]);
        $todos->save();

        return response()->json('The Task successfully added');
    }

    // update todo
    public function update(Request $request)
    {
        $task = Task::find($request->id);
        $task->update($request->all());

        return response()->json('The Task successfully updated');
    }

    // clear todo
    public function clear()
    {
        $task = Task::where('completed', '=', 1);
        $task->delete();
    }

    // delete todo
    public function delete($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
    }

    // complete todo
    public function complete($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = ! $task->completed;
        $task->save();
    }

    // all complete/active todo
    public function action($token)
    {
        Task::where('id', '!=', 0)->update(['completed' => $token]);
    }
}
