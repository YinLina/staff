<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentType\DocumentData;
use App\Http\Resources\Document\DocumentResource;
use App\Models\DocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentController extends Controller
{
    public function read () {
        $document = DocumentModel::all();
        return new DocumentResource($document);
    }

    public function readDocumentTypeForCombobox() {
        $this->CreateDocumentTypeTemporary();
        $documentType = DB::table('temp_DocumentType')->get();
        Schema::drop('temp_DocumentType');
        return new DocumentData($documentType);
    }

    public function create (Request $request) {
        $this->validate($request,
        [
            'DocumentCode' => 'required',
            'Title' => 'required',
            'Type' => 'required',
        ],
        [
            'Document.required' => 'DocumentCode field is required.'
        ]);
        $document = new DocumentModel();
        $document -> DocumentCode = $request->DocumentCode;
        $document -> Title = $request->Title;
        $document -> Type = $request->Type;
        $document -> ContentKH = $request->ContentKH;
        $document -> ContentEN = $request->ContentEN;
        $document -> DescriptionKH = $request->DescriptionKH;
        $document -> DescriptionEN = $request->DescriptionEN;
        if($request->FileName != "") {
            $document->FileName = $request->FileName;
            $document->AttachFile = base64_encode($request->FileUpload);
        }
        $document -> save();
        return response()->json(['message' => 'Document created successfully'], 200);
    }

    public function update (Request $request, $id) {
        $this->validate($request,
        [
            'DocumentCode' => 'required',
            'Title' => 'required',
            'Type' => 'required',
        ],
        [
            'Document.required' => 'DocumentCode field is required.'
        ]);
        $document = DocumentModel::findOrFail($id);
        $document -> DocumentCode = $request->DocumentCode;
        $document -> Title = $request->Title;
        $document -> Type = $request->Type;
        $document -> ContentKH = $request->ContentKH;
        $document -> ContentEN = $request->ContentEN;
        $document -> DescriptionKH = $request->DescriptionKH;
        $document -> DescriptionEN = $request->DescriptionEN;
        if($request->FileName != "") {
            $document->FileName = $request->FileName;
            $document->AttachFile = base64_encode($request->FileUpload);
        }
        $document -> save();
        return response()->json(['message' => 'Document updated successfully'], 200);
    }

    public function delete (Request $request) {
        $selectIds = $request->selectedIds;
        foreach($selectIds as $id) {
            $document = DocumentModel::findOrFail($id);
            $document->delete();
        }
        return response()->json(['message' => 'Document deleted successfully.'], 200);
    }

    public function download($id) {
        $document = DocumentModel::findOrFail($id);
        $contents = base64_decode($document->AttachFile, true);
        return response($contents)->header('FileName', $document->FileName);
    }
}
