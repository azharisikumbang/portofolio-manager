<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExperienceRequest;
use App\Models\Experience;
use App\Utils\Paginator;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $httpRequestAttributes = request()->toArray();
        $perPage = $httpRequestAttributes['limit'] ?? Paginator::OFFSET;
        $experiences = Experience::when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute(
                $httpRequestAttributes['order_by'] ?? 'id', 
                $httpRequestAttributes['order_as'] ?? null)
            )
            ->paginate($perPage);

        return view('admin.experiences.index', $experiences->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExperienceRequest $request)
    {
        $isCreated = Experience::create($request->validated());

        if (!$isCreated) {
            return back()
                ->with(['status' => 'failed', 'messages' => 'Failed to store data.'])
                ->withInput();
        }

        return redirect()
            ->route('experiences.index')
            ->with(['status' => 'success', 'messages' => 'Succcessfully created.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExperienceRequest $request, $id)
    {
        $isUpdated = Experience::findOrFail($id)->update($request->validated());

        if (!$isUpdated) {
            return back()
                ->with(['status' => 'failed', 'messages' => 'Failed to update the data.'])
                ->withInput();
        }

        return redirect()
            ->route('experiences.index')
            ->with(['status' => 'success', 'messages' => 'Succcessfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Experience::findOrFail($id)->delete();

        return redirect()
            ->route('experiences.index')
            ->with(['status' => 'success', 'messages' => 'Succcessfully deleted.']);
    }
}
