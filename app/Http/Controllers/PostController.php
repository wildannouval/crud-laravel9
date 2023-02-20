<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
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

    public function edit(Post $post)
    {
        //render view form edit dan fetching database
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        //validate form
        $this->validate($request,[
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        //check apakah gambar sudah di upload
        if($request->hasFile('image')){

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post with new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content
            ]);

        }else{

            //update post without image
            $post ->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil Diubah!']);
    }

    public function destroy(Post $post)
    {
        //delete image
        Storage::delete('public/posts/'.$post->image);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
