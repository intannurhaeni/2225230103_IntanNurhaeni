<?php

namespace App\Http\Controllers;

//import Model "Post
use App\Models\Post;

use Illuminate\Http\Request;

//import Facade "Storage"
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $posts = Post::latest()->paginate(5);

        //render view with posts
        return view('posts.index', compact('posts'));
    }

    /**
     * create
     *
     * @return View
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * store
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'foto'     => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'nama'     => 'required|min:5',
            'nim'      => 'required|min:5'
        ]);

        //upload foto
        $image = $request->file('foto');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        Post::create([
            'foto'     => $image->hashName(),
            'nama'     => $request->nama,
            'nim'      => $request->nim
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.show', compact('post'));
    }

    /**
     * edit
     *
     * @param  mixed $id
     * @return void
     */
    public function edit($id)
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.edit', compact('post'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, $id)
    {
        //validate form
        $this->validate($request, [
            'foto'     => 'image|mimes:jpeg,jpg,png|max:2048',
            'nama'     => 'required|min:5',
            'nim'   => 'required|min:5'
        ]);

        //get post by ID
        $post = Post::findOrFail($id);

        //check if foto is uploaded
        if ($request->hasFile('foto')) {

            //upload new foto
            $image = $request->file('foto');
            $image->storeAs('public/posts', $image->hashName());

            //delete old foto
            Storage::delete('public/posts/'.$post->foto);

            //update post with new foto
            $post->update([
                'foto'     => $image->hashName(),
                'nama'     => $request->nama,
                'nim'      => $request->nim
            ]);

        } else {

            //update post without foto
            $post->update([
                'nama'     => $request->nama,
                'nim'   => $request->nim
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy($id)
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //delete foto
        Storage::delete('public/posts/'. $post->foto);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}