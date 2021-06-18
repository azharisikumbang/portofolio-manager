<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillPostRequest;
use App\Utils\Paginator;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
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
        $skills = Skill::when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute(
                $httpRequestAttributes['order_by'] ?? 'id', 
                $httpRequestAttributes['order_as'] ?? null)
            )
            ->paginate($perPage);

        return view('admin.skill.index', $skills->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SkillPostRequest $request)
    {
        $isCreated = Skill::create($request->validated());

        if (!$isCreated) {
            return back()
                ->with(['status' => 'failed', 'messages' => 'Failed to store data.'])
                ->withInput();
        }

        return redirect()
            ->route('skills.index')
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
        $skill = Skill::findOrFail($id);

        return view('', ['skill' => $skill]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $skill = Skill::findOrFail($id);

        return view('', ['skill' => $skill]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SkillPostRequest $request, $id)
    {
        $isUpdated = (Skill::findOrFail($id))->update($request->validated());

        if (!$isUpdated) {
            return back()
                ->with(['status' => 'failure', 'messages' => 'Failed to update skill.'])
                ->withInput();
        }

        return redirect()
            ->route('skills.show', ['skill' => $id])
            ->with(['status' => 'success', 'messages' => 'Skill updated succesfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Skill::findOrFail($id)->delete();

        return redirect()
            ->route('skills.index')
            ->with(['status' => 'success', 'messages' => 'Skill updated succesfully.']);
    }
}
