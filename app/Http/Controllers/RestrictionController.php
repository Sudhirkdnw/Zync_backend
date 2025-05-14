<?php

namespace App\Http\Controllers;

use App\Models\UsernameRestriction;
use Illuminate\Http\Request;

class RestrictionController extends Controller
{
   

    public function restrictions()
    {
        $restrictions = UsernameRestriction::get();

        return view('restrictions', [
            'restrictions' => $restrictions,
        ]);
    }

    public function usernameRestrictionsList(Request $request)
    {
        $query = UsernameRestriction::query();
        $totalData = $query->count();

        $columns = ['id'];

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns[$request->input('order.0.column')];
        $orderDir = $request->input('order.0.dir');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where('title', 'LIKE', "%{$searchValue}%");
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = $result->map(function ($item) {
            $edit = "<a rel='{$item->id}' data-title='{$item->title}' class='me-2 btn btn-success px-4 text-white edit' data-tooltip='Edit'>" . __('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') . "</a>";
            $delete = "<a href='#' class='btn btn-danger px-4 text-white delete' rel='{$item->id}' data-tooltip='Delete'>" . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . "</a>";
            $action = "<div class='text-end action'>{$edit}{$delete}</div>";
            return [
                $item->title,
                $action,
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function addUsernameRestrict(Request $request)
    {
        $username = UsernameRestriction::where('title', $request->title)->first();

        if ($username != null) {
            return response()->json([
                'status' => false,
                'message' => 'Username is already exist!',
            ]);
        } else {

            $searchForValue = ',';
            $stringValue = $request->title;

            if (strpos($stringValue, $searchForValue) !== false) {

                $usernameArrays = explode(',', $stringValue);
                foreach ($usernameArrays as $usernameArray) {
                    $username = new UsernameRestriction();
                    $username->title = $usernameArray;
                    $username->save();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Username Add Successfully',
                    'data' => $username,
                ]);
            }

            $username = new UsernameRestriction();
            $username->title = $request->title;
            $username->save();

            return response()->json([
                'status' => true,
                'message' => 'Username Add Successfully',
                'data' => $username,
            ]);
        }
    }

    public function updateUsernameRestrict(Request $request)
    {
        $username = UsernameRestriction::where('id', $request->username_id)->first();
        if (!$username) {  
            return response()->json([
                'status' => false,
                'message' => 'Username not found',
            ]);
        } 

        $username->title = $request->title;
        $username->save();

        return response()->json([
            'status' => true,
            'message' => 'Username Updated Successfully',
        ]);
    }

    public function deleteUsernameRestrictions(Request $request)
    {
        $username = UsernameRestriction::where('id', $request->username_id)->first();
        if (!$username) {  
            return response()->json([
                'status' => false,
                'message' => 'Username not found',
            ]);
        }
 
        $username->delete();

        return response()->json([
            'status' => true,
            'message' => 'Username Deleted Successfully',
        ]);
    }



}
