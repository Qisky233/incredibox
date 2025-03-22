<?php

namespace App\Http\Controllers;

use App\Models\GList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GListController extends Controller
{
    // 获取所有记录
    public function index()
    {
        $lists = GList::all();
        return response()->json($lists);
    }

    // 创建新记录
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string|max:5000',
            'info' => 'nullable|string|max:5000',
            'coverUrl' => 'nullable|string|max:255',
            'iframe' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $list = GList::create($request->all());
        return response()->json($list, 201);
    }

    // 获取单条记录
    public function show($id)
    {
        $list = GList::find($id);
        if (!$list) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($list);
    }

    public function getByTitle(Request $request)
    {
        $title = $request->query('title');
        if (!$title) {
            return response()->json(['message' => 'Title parameter is required'], 400);
        }

        $game = GList::where('title', $title)->get();
        return response()->json($game);
    }

    // 更新记录
    public function update(Request $request, $id)
    {
        $list = GList::find($id);
        if (!$list) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'desc' => 'sometimes|string|max:5000',
            'info' => 'sometimes|string|max:5000',
            'coverUrl' => 'sometimes|string|max:255',
            'iframe' => 'sometimes|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $list->update($request->all());
        return response()->json($list);
    }

    // 删除记录
    public function destroy($id)
    {
        $list = GList::find($id);
        if (!$list) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $list->delete();
        return response()->json(['message' => 'Record deleted']);
    }
}
