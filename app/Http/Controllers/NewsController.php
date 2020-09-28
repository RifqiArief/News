<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\News;
use App\Models\NewsDetail;
use DB;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function create(Request $request) {
        if ($request->user()->id_role == 1){
           
            $validator = Validator::make($request->all(), [
                'title'         => 'required|min:6',
                'image'         => 'required',
                'content'       => 'required',
            ]);

            if ($validator->fails()) {
                $res = array(
                    'code'      => 99,
                    'message'   => $validator->messages()->first(),
                );
                return response()->json($res, 200);
            }

            $idUser     = $request->user()->id;
            $namaUser   = $request->user()->name;

            try{
                $news = new News;
                $news->source       = $request->source;
                $news->title        = $request->title;
                $news->image        = $request->image;
                $news->created_by   = $request->user()->name;
                
                $news->save();
                $idNews         = $news->id;

                $newsDetail = new NewsDetail;
                $newsDetail->id_user    = $idUser;
                $newsDetail->id_news    = $idNews;
                $newsDetail->content    = $request->content;       
                $newsDetail->save();

                $res = array(
                    'code'      => 0,
                    'message'   => 'sukses',
                );
                return response()->json($res, 200);
            } catch(\Exception $ex) {
                $res = array(
                    'code'      => 99,
                    'message'   => $ex->getMessage(),
                );
                return response()->json($res, 200);
            }
        }else{
            $res = array(
                'code'      => 99,
                'message'   => 'anda tidak diizinkan',
            );
            return response()->json($res, 200);
        }
    }

    public function getAll(){
        $news = DB::table('news')
                ->select('id_news','source', 'title', 'image', 'created_at as publised_at')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'asc')
                ->get();

        $res = array(
            'code'      => 0,
            'message'   => 'sukses',
            'data'      => $news
        );
        return response()->json($res, 200);
    }

    public function getDetail(){

    }

    public function delete(Request $request) {
        if ($request->user()->id_role == 1){
           
            $validator = Validator::make($request->all(), [
                'id_news'       => 'required',
            ]);

            if ($validator->fails()) {
                $res = array(
                    'code'      => 99,
                    'message'   => $validator->messages()->first(),
                );
                return response()->json($res, 200);
            }

            $now = Carbon::now();

            try{
                News::where('id_news', $request->id_news)
                    ->update([
                        'deleted_by'    => $request->user()->name,
                        'deleted_at'    => $now
                    ]);

                $res = array(
                    'code'      => 0,
                    'message'   => 'sukses',
                );
                return response()->json($res, 200);
            } catch(\Exception $ex) {
                $res = array(
                    'code'      => 99,
                    'message'   => $ex->getMessage(),
                );
                return response()->json($res, 200);
            }
        }else{
            $res = array(
                'code'      => 99,
                'message'   => 'anda tidak diizinkan',
            );
            return response()->json($res, 200);
        }
    }

    public function update(Request $request) {
        if ($request->user()->id_role == 1){
           
            $validator = Validator::make($request->all(), [
                'id_news'       => 'required',
                'title'         => 'required|min:6',
                'image'         => 'required',
                'content'       => 'required',
            ]);

            if ($validator->fails()) {
                $res = array(
                    'code'      => 99,
                    'message'   => $validator->messages()->first(),
                );
                return response()->json($res, 200);
            }

            $now = Carbon::now();

            try{
                News::where('id_news', $request->id_news)
                    ->update([
                        'title'         => $request->title,
                        'image'         => $request->image,
                        'source'        => $request->source,
                        'updated_by'    => $request->user()->name
                    ]);

                NewsDetail::where('id_news', $request->id_news)
                    ->update([
                        'content'       => $request->title,
                    ]);

                $res = array(
                    'code'      => 0,
                    'message'   => 'sukses',
                );
                return response()->json($res, 200);
            } catch(\Exception $ex) {
                $res = array(
                    'code'      => 99,
                    'message'   => $ex->getMessage(),
                );
                return response()->json($res, 200);
            }
        }else{
            $res = array(
                'code'      => 99,
                'message'   => 'anda tidak diizinkan',
            );
            return response()->json($res, 200);
        }
    }
}
