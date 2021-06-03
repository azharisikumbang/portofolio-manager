<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationPostRequest;
use App\Models\Education;
use App\Utils\Paginator;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $httpRequestAttributes = request()->toArray();
        $perPage = $httpRequestAttributes['limit'] ?? 10;
        $educations = Education::when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute(
                $httpRequestAttributes['order_by'] ?? 'id', 
                $httpRequestAttributes['order_as'] ?? null)
            )
            ->paginate($perPage);

        return view('admin.education.index', $educations->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.education.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EducationPostRequest $request)
    {
        Education::create($request->validated());

        return redirect()->route('educations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $education = Education::findOrFail($id);

        return view('admin.education.detail', ['education' => $education]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $education = Education::findOrFail($id);

        return view('admin.education.edit', ['education' => $education]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EducationPostRequest $request, $id)
    {
        $isUpdated = Education::findOrFail($id)
                        ->update($request->validated());

        if (!$isUpdated) {
            return redirect()->route('educations.edit')->withInput();
        }

        return redirect()
            ->route('educations.show', ['education' => $id])
            ->with('status', 'Education updated succesfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $education = Education::findOrFail($id);
        $education->delete();

        return redirect()->route('education.index');
    }
}
