<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Utils\Paginator;
use App\Http\Requests\CertificationCreateRequest;
use App\Http\Requests\CertificationUpdateRequest;
use Illuminate\Http\Request;

class CertificationController extends Controller
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
        $certifications = Certification::when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute(
                $httpRequestAttributes['order_by'] ?? 'id', 
                $httpRequestAttributes['order_as'] ?? null)
            )
            ->paginate($perPage);

        return view('admin.certification.index', $certifications->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CertificationCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CertificationCreateRequest $request)
    {
        $isCreated = Certification::create($request->validated());

        if (!$isCreated) {
            return back()
                ->with(['status' => 'failed', 'messages' => 'Failed to store data.'])
                ->withInput();
        }

        return redirect()
            ->route('certifications.index')
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
        return view('admin.certification.detail', [
            'data' => Certification::findOrFail($id)
        ]);
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
    public function update(CertificationUpdateRequest $request, $id)
    {
        $isUpdated = (Certification::findOrFail($id))->update($request->validated());

        if (!$isUpdated) {
            return back()
                ->with(['status' => 'failure', 'messages' => 'Failed to update certification data.'])
                ->withInput();
        }

        return redirect()
            ->route('certifications.show', ['certification' => $id])
            ->with(['status' => 'success', 'messages' => 'Certification updated succesfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Certification::findOrFail($id)
            ->delete();

        return redirect()
            ->route('certifications.index')
            ->with(['status' => 'success', 'messages' => 'Certification deleted succesfully.']);
    }
}
