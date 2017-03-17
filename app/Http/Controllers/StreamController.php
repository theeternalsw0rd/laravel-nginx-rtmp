<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Stream;

class StreamController extends BaseController
{
    public function __construct()
    {
        if($_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            $this->middleware('auth');
        }
    }

    public function auth(Request $request)
    {
        if($request->input('app') == "show" && $request->input('addr') == '127.0.0.1') return "true";
        $stream = Stream::where('name', '=', $request->input('name'))->where('key', '=', $request->input('key'))->firstOrFail();
        return "true";
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $name = str_slug($request->input('name'));
            $description = $request->input('description');
            $key = sha1(time() . $name);
            $stream = new Stream();
            $stream->name = $name;

            $stream->key = $key;
            $stream->description = $description;
            if($stream->save()) {
                return redirect('/');
            }
            else {
                return redirect()->back();
            }
        }
        if($request->isMethod('get'))
        {
            return view('stream.create')->with('title', 'Create Stream');
        }
    }

    public function delete($id)
    {
        $stream = Stream::findOrFail($id);
        $stream->delete();
        return redirect()->back();
    }
}
