<?php
namespace App\Http\Controllers;

use Validator;
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
            $name = $request->input('name');
            $slug = str_slug($name);
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:streams'
            ]);
            $validator->after(function($validator) use($slug) {
                if(count($validator->errors()) > 0) {
                    foreach($validator->errors()->messages() as $field => $errors) {
                        if($field == 'name') {
                            return;
                        }
                    }
                }
                $streams = Stream::where('slug', '=', $slug)->get();
                if($streams->count() > 0) {
                    $validator->errors()->add('slug', 'The name you have provided may have unique punctuation, but the url slug is already in use.');
                }
            });
            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $title = $request->input('title');
            $byline = $request->input('byline');
            $key = sha1(time() . $slug);
            $stream = new Stream();
            $stream->name = $name;
            $stream->slug = $slug;
            $stream->title = $title;
            $stream->byline = $byline;
            $stream->key = $key;
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

    public function edit(Request $request, $id)
    {
        if($request->isMethod('post'))
        {
            $name = $request->input('name');
            $slug = str_slug($name);
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:streams,id,' . $id
            ]);
            $validator->after(function($validator) use($slug, $id) {
                if(count($validator->errors()) > 0) {
                    foreach($validator->errors()->messages() as $field => $errors) {
                        if($field == 'name') {
                            return;
                        }
                    }
                }
                $streams = Stream::where('slug', '=', $slug)->get();
                if($streams->count() > 0) {
                    if($streams->first()->id == $id) {
                        return;
                    }
                    $validator->errors()->add('slug', 'The name you have provided may have unique punctuation, but the url slug is already in use.');
                }
            });
            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $title = $request->input('title');
            $byline = $request->input('byline');
            $stream = Stream::findOrFail($id);
            $stream->name = $name;
            $stream->slug = $slug;
            $stream->title = $title;
            $stream->byline = $byline;
            if($stream->save()) {
                return redirect('/');
            }
            else {
                return redirect()->back();
            }
        }
        if($request->isMethod('get'))
        {
            $stream = Stream::findOrFail($id);
            return view('stream.edit')->with([
                'title' => 'Edit Stream',
                'name' => $stream->name,
                'streamTitle' => $stream->title,
                'byline' => $stream->byline
            ]);
        }

    }

    public function delete($id)
    {
        $stream = Stream::findOrFail($id);
        $stream->delete();
        return redirect()->back();
    }
}
