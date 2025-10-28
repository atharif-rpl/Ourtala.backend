<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index()
    {
        return response()->json(TeamMember::all());
    }

    public function store(Request $request)
    {
        $member = TeamMember::create($request->all());
        return response()->json($member, 201);
    }

    public function show($id)
    {
        return response()->json(TeamMember::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $member = TeamMember::findOrFail($id);
        $member->update($request->all());
        return response()->json($member);
    }

    public function destroy($id)
    {
        TeamMember::destroy($id);
        return response()->json(['message' => 'Team member deleted']);
    }
}
