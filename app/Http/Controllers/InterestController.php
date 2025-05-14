<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\Room;
use Illuminate\Http\Request;

class InterestController extends Controller
{
   public function interests()
   {
        return view('interest');
   }

   public function interestList(Request $request)
    {
        $totalData = Interest::count();
        $rows = Interest::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'image',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Interest::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = Interest::Where('title', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Interest::Where('title', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
            $edit = '<a href="#" data-title="' . $item->title . '" class="me-3 btn btn-success px-4 text-white edit" rel=' . $item->id . ' data-tooltip="Edit">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete" rel=' . $item->id . ' data-tooltip="Delete">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . '</a>';
            $action = '<span class="float-right">'. $edit . $delete .' </span>' ;

            $data[] = [
                $item->title,
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }
    
   public function addInterest(Request $request)
   {
        $interestList = Interest::where('title', $request->title)->get()->first();

        if ($interestList != null) {
            return response()->json([
                'status' => false,
                'message' => 'interest Record Dublicate',
            ]);
        } else {

            $interest = new Interest;
            $interest->title = $request->title;
            $interest->save();

            return response()->json([
                'status' => true,
                'message' => 'interest Added Successfully',
                'data' => $interest,
            ]);
        }

   }

   public function updateInterest(Request $request)
   {
       
    $interestList = Interest::where('title', $request->title)->first();

    if ($interestList != null) {
        return response()->json([
            'status' => false,
            'message' => 'interest Record Dublicate',
        ]);
    } else {

        $interest = Interest::where('id', $request->interest_id)->first();
        if ($interest) {
            $interest->title = $request->title;
            $interest->save();
 
            return response()->json([
                'status' => true,
                'message' => 'Interest Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Interest Not Found',
            ]);
        }
    }
    

       
   }

   public function deleteInterest($id)
   {
       $interest = Interest::find($id);

       if ($interest) {
           $interest->delete();
           return response()->json([
               'status' => true,
               'message' => 'Interest Delete Successfully',
           ]);
       } else {
           return response()->json([
               'status' => false,
               'message' => 'Interest Not Found',
           ]);
       }
   }

   public function fetchInterests()
   {
    $fetchInterests = Interest::all();

    foreach ($fetchInterests as $fetchInterest) {
            
        $interestRoomsCount = Room::where('interest_ids', $fetchInterest->id)->count();
        
        $fetchInterest->totalRoomOfInterest = $interestRoomsCount;
    }

    return response()->json([
        'status' => true,
        'message' => 'Fetch Interest',
        'data' => $fetchInterests,
    ]);
   }
}