<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DocumentSubmissionRequest;
use App\Http\Requests\DocumentAttachmentRequest;
use App\Models\DocumentSubmission;
use App\Models\DocumentAttachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Enums\SubmissionStatusEnum;
use DB;

class DocumentController extends Controller
{
    public function store(DocumentSubmissionRequest $request)
    {
        $data = $request->validated();
        $details = Arr::pull($data, 'document_attachments');

        DB::beginTransaction();
        try {
            $documentSubmission = DocumentSubmission::create([
                ...$data,
                'user_id' => auth()->user()->id,
                'status' => SubmissionStatusEnum::CREATED,
            ]);
            $documentAttachments = [];
            foreach ($details as $detail) {
                $filePath = Storage::disk('public')->put('submissions', $detail['file']);
                $documentAttachment = DocumentAttachment::create([
                    ...$detail,
                    'file_path' => $filePath,
                    'document_submission_id' => $documentSubmission->id,
                ]);
                array_push($documentAttachments, $documentAttachment);
            }

            DB::commit();
            return response()->api([...$documentSubmission->toArray(), 'document_attachments' => $documentAttachments], 200, 'ok', 'Berhasil mengajukan dokumen');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal mengajukan dokumen');
        }
    }

    public function index(Request $request)
    {
        $documentSubmission = DocumentSubmission::search();
        $result = [
            'count' => $documentSubmission->count(),
            'document_submissions' => $documentSubmission->getResult()->load('document_attachments'),
        ];

        return response()->api($result, 200, 'ok', 'Berhasil mendapatkan data pengajuan');
    }

    public function show(Request $request, DocumentSubmission $submission)
    {
        return response()->api($submission->load('document_attachments'), 200, 'ok', 'Berhasil mendapatkan detil pengajuan');
    }

    public function storeAttachment(DocumentAttachmentRequest $request, DocumentSubmission $submission)
    {
        if($submission->status === SubmissionStatusEnum::COMPLETE) {
            return response()->api([], 400, 'error', 'Pengajuan telah selesai dikerjakan');
        }

        $data = $request->validated();
        DB::beginTransaction();
        try {
            $filePath = Storage::disk('public')->put('submissions', $data['file']);
            $documentAttachment = DocumentAttachment::create([
                ...$data,
                'file_path' => $filePath,
                'document_submission_id' => $submission->id,
            ]);

            DB::commit();
            return response()->api($documentAttachment, 200, 'ok', 'Berhasil mengunggah dokumen');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal mengunggah dokumen');
        }
    }

    public function deleteAttachment(DocumentSubmission $submission, DocumentAttachment $attachment)
    {
        if($attachment->document_submission_id !== $submission->id) {
            return response()->api([], 400, 'error', 'Attachment not belongs to document submission');
        }

        if($submission->status === SubmissionStatusEnum::COMPLETE) {
            return response()->api([], 400, 'error', 'Pengajuan telah selesai dikerjakan');
        }

        Storage::disk('public')->delete($attachment->file_path);
        DocumentAttachment::destroy($attachment->id);

        return response()->api([], 200, 'ok', 'Berhasil menghapus dokumen');
    }
}
