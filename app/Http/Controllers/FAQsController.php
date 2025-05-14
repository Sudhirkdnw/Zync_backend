<?php

namespace App\Http\Controllers;

use App\Models\FAQs;
use App\Models\FAQsType;
use Illuminate\Http\Request;

class FAQsController extends Controller
{
    public function fetchFAQs(Request $request)
    {
        
        $faqs = FAQsType::where('is_deleted', 0)->with('faqs')->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetching FAQs successfully',
            'data' => $faqs
        ]);

    }

    public function faqs()
    {
        $FAQsAllRecords = FAQs::all();
        $FAQsAllTypes = FAQsType::orderBy('id', 'DESC')->get();

        return view('faqs', [
            'FAQsAllRecords' => $FAQsAllRecords,
            'FAQsAllTypes' => $FAQsAllTypes
        ]);
    }
 
    public function faqsList(Request $request)
    {
        $totalData = FAQs::with('type')->count();
        $rows = FAQs::with('type')->orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'user_id',
            2 => 'subject',
            3 => 'description',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = FAQs::with('type')->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = FAQs::with('type')->Where('question', 'LIKE', "%{$search}%")->orWhere('answer', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = FAQs::with('type')->Where('question', 'LIKE', "%{$search}%")->orWhere('answer', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {

            $FAQsQuestion =  $item->question;
            $FAQsAnswer =  $item->answer;
 
            $FAQs = '<div class="faqs_table_blog"><span class="question">' . $FAQsQuestion . ' </span><span class="answer">' . $FAQsAnswer . ' </span> </div>';
            
            $edit = '<a href="#" class="ms-3 btn btn-success px-4 text-white edit" rel=' . $item->id . ' data-tooltip="Edit" data-question="'. $item->question .'" data-answer="'. $item->answer .'" data-faqtype="'. $item->type->id .'" >' . __('<svg data-name="Layer 1" height="200" id="Layer_1" viewBox="0 0 200 200" width="200" xmlns="http://www.w3.org/2000/svg"><title></title><path d="M170,70.5a10,10,0,0,0-10,10V140a20.06,20.06,0,0,1-20,20H60a20.06,20.06,0,0,1-20-20V60A20.06,20.06,0,0,1,60,40h59.5a10,10,0,0,0,0-20H60A40.12,40.12,0,0,0,20,60v80a40.12,40.12,0,0,0,40,40h80a40.12,40.12,0,0,0,40-40V80.5A10,10,0,0,0,170,70.5Zm-77,39a9.67,9.67,0,0,0,14,0L164.5,52a9.9,9.9,0,0,0-14-14L93,95.5A9.67,9.67,0,0,0,93,109.5Z" fill="#fff"></path></svg>') . '</a>';
            $delete = '<a href="#" class="ms-3 btn btn-danger px-4 text-white delete" rel=' . $item->id . ' data-tooltip="Delete" >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . '</a>';
            $action = '<span class="float-right">' . $edit . $delete . ' </span>';

            $data[] = [
                $FAQs,
                $item->type->title,
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

    public function addFAQs(Request $request)
    {
        $faqs = new FAQs();
        $faqs->faqs_type_id = $request->faqs_type_id;
        $faqs->question = $request->question;
        $faqs->answer = $request->answer;
        $faqs->save();

        return response()->json([
            'status' => true,
            'message' => 'FAQs Added Successfully',
            'data' => $faqs,
        ]);
    }

    public function updateFAQs(Request $request) 
    { 
        $faqs = FAQs::where('id', $request->FAQs_id)->first();
        if ($faqs) {
            $faqs->faqs_type_id = $request->faqs_type_id;
            $faqs->question = $request->question;
            $faqs->answer = $request->answer;
            $faqs->save();

            return response()->json([
                'status' => true,
                'message' => 'FAQs Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Support Subject Not Found',
            ]);
        } 
    }

    public function deleteFAQs(Request $request)
    {

        $faqs = FAQs::where('id', $request->faqs_id)->first();
        if ($faqs) {
            $faqs->delete();

            return response()->json([
                'status' => true,
                'message' => 'FAQs Delete Successfully',
                'data' => $faqs
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Report Not Found',
            ]);
        }
    }
    
    public function deleteFAQsType(Request $request)
    {

        $faqsType = FAQsType::where('id', $request->faqsType_id)->first();
        if ($faqsType) {

            $faqs = FAQs::where('faqs_type_id', $request->faqsType_id)->get();
            if ($faqs) {
                $faqs->each->delete(); 
            }

            $faqsType->delete();

            return response()->json([
                'status' => true,
                'message' => 'FAQs Type Delete Successfully',
                'data' => $faqsType
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'FAQs Type Not Found',
            ]);
        }
    }

    public function faqsTypeList(Request $request)
    {
        $totalData = FAQsType::count();
        $rows = FAQsType::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'title',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = FAQsType::offset($start)->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search = $request->input('search.value');
            $result = FAQsType::Where('title', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = FAQsType::Where('title', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
           
            $edit = '<a href="#" data-title="' . $item->title . '" class="me-3 btn btn-success px-4 text-white edit" rel=' . $item->id . ' data-tooltip="Edit">' . __(' <svg data-name="Layer 1" height="200" id="Layer_1" viewBox="0 0 200 200" width="200" xmlns="http://www.w3.org/2000/svg"><title/><path d="M170,70.5a10,10,0,0,0-10,10V140a20.06,20.06,0,0,1-20,20H60a20.06,20.06,0,0,1-20-20V60A20.06,20.06,0,0,1,60,40h59.5a10,10,0,0,0,0-20H60A40.12,40.12,0,0,0,20,60v80a40.12,40.12,0,0,0,40,40h80a40.12,40.12,0,0,0,40-40V80.5A10,10,0,0,0,170,70.5Zm-77,39a9.67,9.67,0,0,0,14,0L164.5,52a9.9,9.9,0,0,0-14-14L93,95.5A9.67,9.67,0,0,0,93,109.5Z" fill="#fff"/></svg>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete" rel=' . $item->id . ' data-tooltip="Delete">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . '</a>';
            $action = '<span class="float-right">' . $edit . $delete . ' </span>';

            $data[] = [$item->title, $action];

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

    public function addFAQsType(Request $request)
    {
        $faqsType = FAQsType::where('title', $request->title)->first();

        if ($faqsType != null) {
            return response()->json([
                'status' => false,
                'message' => 'FAQs Type Already Exist',
            ]);
        } else {
            $faqsType = new FAQsType();
            $faqsType->title = $request->title;
            $faqsType->save();

            return response()->json([
                'status' => true,
                'message' => 'FAQs Type Added Successfully',
                'data' => $faqsType,
            ]);
        }
    }

    public function updateFAQsType(Request $request) 
    {
        $faqsType = FAQsType::where('title', $request->title)->first();

        if ($faqsType != null) {
            return response()->json([
                'status' => false,
                'message' => 'FAQs Type Record Dublicate',
            ]);
        } else {
            $faqsType = FAQsType::where('id', $request->faqsType_id)->first();
            if ($faqsType) {
                $faqsType->title = $request->title;
                $faqsType->save();

                return response()->json([
                    'status' => true,
                    'message' => 'FAQs Type Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Support Subject Not Found',
                ]);
            }
        }
    }

}
