<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OperatorStoreRequest;
use App\Http\Requests\OperatorUpdateRequest;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        $data['operators'] = Operator::all();

        return view('admin.operator.index', $data);
    }

    public function store(OperatorStoreRequest $request)
    {
        $data = $request->all();

        Operator::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title'])
        ]);

        return redirect()->back()->with('success', 'Оператор успешно добавлен!');
    }

    public function show(Request $request)
    {
        $data = $request->all();
        $operator = Operator::find($data['id']);

        if (empty($operator)) {
            $operator = [];
        }

        return response()->json([
            'operator' => $operator
        ]);
    }

    public function update(OperatorUpdateRequest $request)
    {
        $data = $request->all();

        Operator::where('id', $data['id'])->update([
            'title' => $data['title'],
            'slug' => Str::slug($data['title'])
        ]);

        return redirect()->back()->with('success', 'Оператор успешно отредактирован!');
    }

    public function destroy(Request $request)
    {
        $data = $request->all();
        $operator = Operator::find($data['id']);

        if ($operator) {
            $operator->delete();
        }

        return redirect()->back()->with('success', 'Оператор успешно удален!');
    }
}
