<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\News;
use App\Models\NewsDetail;
use App\Models\Comment;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class NewsController extends Controller
{
    public function saveImage($file)
    {
        try{
            $extension = $file->extension();
            $fileName = Carbon::now()->timestamp . '.' . $extension;
            $path = Storage::putFileAs('public/images', $file, $fileName);
     
            $res = array(
                'code'      => true,
                'message'   => $fileName
            );
    
            return $res;
        }catch(\Exception $ex){
            $res = array(
                'code'      => false,
                'message'   => 'gagal menyimpan gambar, '. $ex->getMessage(),
            );

            return $res;
        }
    }

    public function getImage($fileName){
        try{
            $storagePath = storage_path('public/images/' . $fileName);
            return Image::make($storagePath)->response();
    
        } catch(\Exception $ex) {
            $res = array(
                'code'      => 99,
                'message'   => $ex->getMessage(),
            );
            return response()->json($res, 200);
        }
    }

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

            $saveImage = $this->saveImage($request->image); 

            if($saveImage['code'] == false){
                $res = array(
                    'code'      => 99,
                    'message'   =>  $saveImage['message']
                );
                return response()->json($res, 200);
            }
            
            try{
                $news = new News;
                $news->source       = $request->source;
                $news->title        = $request->title;
                $news->image        = $saveImage['message'];
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
        $news = DB::table('news_detail as d')
                ->select('d.id_news_detail as id_news', 'n.source', 'n.title', 'n.image', 'n.created_at as published_at')
                ->join('news as n', 'd.id_news', '=', 'n.id_news')
                ->whereNull('n.deleted_at')
                ->orderBy('n.created_at', 'asc')
                ->paginate(10);

        $res = array(
            'code'      => 0,
            'message'   => 'sukses',
            'data'      => $news
        );
        return response()->json($res, 200);
    }

    public function getDetail(Request $request){
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

        try{
            $news = DB::table('news_detail as d')
                    ->select('u.name as author', 'n.source', 'n.title', 'n.image', 'd.content', 'n.created_at as published_at')
                    ->join('users as u', 'd.id_user', '=', 'u.id')
                    ->join('news as n', 'd.id_news', '=', 'n.id_news')
                    ->where('d.id_news_detail', '=', $request->id_news)
                    ->whereNull('n.deleted_at')
                    ->first();

            $comment = DB::table('comments as c')
                    ->select('c.id','c.parent_id', 'u.name','c.comment','c.created_at')
                    ->join('users as u', 'c.user_id', '=', 'u.id')
                    ->orderBy('c.created_at', 'asc')
                    ->get();

            $newsData = array(
                'news'      => $news,
                'comment'   => $comment,
            );

            $res = array(
                'code'      => 0,
                'message'   => 'sukses',
                'data'      => $newsData
            );
            return response()->json($res, 200);

        }catch(\Exception $ex) {
            $res = array(
                'code'      => 99,
                'message'   => $ex->getMessage(),
            );
            return response()->json($res, 200);
        }
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
                        'content'       => $request->content,
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
