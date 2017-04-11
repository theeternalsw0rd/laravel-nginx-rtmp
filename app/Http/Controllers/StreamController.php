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
        $stream = Stream::where('slug', '=', $request->input('name'))->where('key', '=', $request->input('key'))->firstOrFail();
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
            $validator->after(function($validator) use($slug)
            {
                if($slug == 'create')
                {
                    $validator->errors()->add('keyword', 'The name you have provided may be a unique stream name but "create" is a reserved keyword.');
                }
                $streams = Stream::where('slug', '=', $slug)->get();
                if($streams->count() > 0)
                {
                    $validator->errors()->add('slug', 'The name you have provided may have unique punctuation, but the url slug is already in use.');
                }
            });
            if($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $fbPageID = $request->input('fbPageID');
            $fbPageToken = $request->input('fbPageToken');
            $title = $request->input('title');
            $description = $request->input('description');
            $key = sha1(time() . $slug);
            $stream = new Stream();
            $stream->name = $name;
            $stream->slug = $slug;
            $stream->fbPageID = $fbPageID;
            $stream->fbPageToken = $fbPageToken;
            $stream->fbStreamURL = '';
            $stream->title = $title;
            $stream->description = $description;
            $stream->key = $key;
            if($stream->save())
            {
                return redirect('/');
            }
            else
            {
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
            $stream = Stream::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:streams,id,' . $id
            ]);
            $validator->after(function($validator) use($slug, $id)
            {
                if($slug == 'create')
                {
                    $validator->errors()->add('keyword', 'The name you have provided may be a unique stream name but "create" is a reserved keyword.');
                }
                $streams = Stream::where('slug', '=', $slug)->get();
                if($streams->count() > 0)
                {
                    if($streams->first()->id != $id)
                    {
                        $validator->errors()->add('slug', 'The name you have provided may have unique punctuation, but the url slug is already in use.');
                    }
                }
            });
            if($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $fbPageID = $request->input('fbPageID');
            $fbPageToken = $request->input('fbPageToken');
            $title = $request->input('title');
            $description = $request->input('description');
            if(!empty($stream->fbStreamURL)) {
                if($fbPageID !== $stream->fbPageID || $fbPageToken !== $stream->fbPageToken)
                {
                    return redirect()->back()->withErrors(['fbookErr', 'You changed Facebook integration with a running stream. Please stop the existing stream first.'])->withInput();
                }
            }
            $stream->name = $name;
            $stream->slug = $slug;
            $stream->fbPageID = $fbPageID;
            $stream->fbPageToken = $fbPageToken;
            $originalTitle = $stream->title;
            $originalDescription = $stream->description;
            $stream->title = $title;
            $stream->description = $description;
            if($stream->save())
            {
                if(!empty($stream->fbStreamURL) && ($originalTitle != $title || $originalDescription != $description))
                {
                    $fb = app(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);
                    $fb->setDefaultAccessToken($fbPageToken);
                    $videoID = preg_replace('%.*/([^?]*).*%', '${1}', $stream->fbStreamURL);
                    $streamData = [
                        'title' => $title,
                        'description' => $title . ': ' . $description
                    ];
                    try
                    {
                        $response = $fb->post('/' . $videoID, $streamData);
                    }
                    catch(\Facebook\Exceptions\FacebookResponseException $e)
                    {
                        return redirect('/')->withErrors(['err' => 'Could not update Facebook Live title or description. You will need to manually update it through Facebook.']);
                    }
                    catch(\Facebook\Exceptions\FacebookSDKException $e)
                    {
                        return redirect('/')->withErrors(['err' => 'Could not update Facebook Live title or description. You will need to manually update it through Facebook.']);
                    }
                }
                return redirect('/');
            }
            else
            {
                return redirect()->back()->withErrors(['err' => 'Could not save updates due to a server-side issue.']);
            }
        }
        if($request->isMethod('get'))
        {
            $stream = Stream::findOrFail($id);
            return view('stream.edit')->with([
                'title' => 'Edit Stream',
                'name' => $stream->name,
                'streamTitle' => $stream->title,
                'fbPageID' => $stream->fbPageID,
                'fbPageToken' => $stream->fbPageToken,
                'description' => $stream->description
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
