<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\BranchModels;
use App\Http\Resources\Branch\BranchResources;
use DateTime;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function read() {
        $branch = BranchModels::all();
        return new BranchResources($branch);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'branchName' => 'required|string|min:2|max:255',
            'branchCode' => 'required|digits_between:2,5|unique:t_branch,branchCode',
        ],[
            'branchCode.required' => 'The branch code is required.',
            'branchName.required' => 'The branch name is required.',
        ]);
        $branch = new BranchModels();
        $branch->BranchCode = $request->branchCode;
        $branch->BranchName = $request->branchName;
        $branch->Location = $request->location;
        $branch->CreatedDate = $request->createdDate;
        $branch->ClosedDate = $request->closedDate;
        $branch->Remark = $request->remark;
        $branch->save();
        return response()->json(['message' => 'Branch created successfully'], 200);
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'branchName' => 'required',
        ],[
            'branchName.required' => 'The branch name is required.',
        ]);
        $branch = BranchModels::findOrFail($id);
        $branch->BranchCode = $request->branchCode;
        $branch->BranchName = $request->branchName;
        $branch->Location = $request->location;
        $branch->CreatedDate = $request->createdDate;
        $branch->ClosedDate = $request->closedDate;
        $branch->Remark = $request->remark;
        $branch->save();
        return response()->json(['message' => 'Branch updated successfully'], 200);
    }

    public function delete($id) {
        $branch = BranchModels::findOrFail($id);
        $branch->delete();
        return response()->json(['message' => 'Branch deleted successfully'], 200);
    }
}
