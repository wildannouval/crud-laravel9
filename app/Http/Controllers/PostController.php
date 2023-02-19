<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        //get posts
        $posts = Post::latest()->paginate(5);

        //render view with posts
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        //render view halaman insert form
        return view('posts.create');
    }

    public function store(Request $request)
    {
        //Memproses data yang dikirimkan dari halaman insert form

        //validate form
        $this->validate($request,[
            'image' => 'require|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'require|min:5',
            'content' => 'require|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
