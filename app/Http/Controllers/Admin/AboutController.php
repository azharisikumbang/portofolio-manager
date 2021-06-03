<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutRequest;
use App\Models\About;
use App\Utils\FileUploader; 
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aboutData = About::first();

        if (is_null($aboutData)) {
            return redirect()->route('about.create');
        }

        return response()->json(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aboutData = About::first();

        if (!is_null($aboutData)) {
            return redirect()->route('about.index');
        } 

        return response()->json(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Request\AboutRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AboutRequest $request)
    {
        $aboutData = About::first();

        if (!is_null($aboutData)) {
            return redirect()
                ->route('about.index')
                ->withErrors([
                    'message' => 'Failed to store more than one data. You only has authorize to update existing data.'
                ]);
        }

        $validated = $request->validated();

        $photo = FileUploader::uploadAsPhoto($validated['photo']);
        $cv = FileUploader::uploadAsDocument($validated['cv']);

        About::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'photo' => $photo->getClientOriginalName(),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'cv' => $cv->getClientOriginalName()
        ]);

        return redirect()->route('about.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $aboutData = About::first();

        if (is_null($aboutData)) {
            return redirect()->route('about.create');
        }

        return response()->json(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(AboutRequest $request)
    {
        $aboutData = About::first();

        if (is_null($aboutData)) {
            return redirect()
                ->route('about.create')
                ->withInput();
        }

        $validated = $request->validated();

        $photo = FileUploader::uploadAsPhoto($validated['photo']);
        $cv = FileUploader::uploadAsDocument($validated['cv']);

        $aboutData->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'photo' => $photo->getClientOriginalName(),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'cv' => $cv->getClientOriginalName()
        ]);

        return redirect()->route('about.index');
    }
}
