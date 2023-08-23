<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DocumentSubmissionRequest;
use App\Http\Requests\DocumentAttachmentRequest;
use App\Http\Requests\DocumentProgressRequest;
use App\Models\DocumentSubmission;
use App\Models\DocumentAttachment;
use App\Models\DocumentProgress;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Enums\SubmissionStatusEnum;
use App\Enums\ProgressStatusEnum;
use App\Enums\SubmissionStageEnum;
use App\Enums\UserRoleEnum;
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
                'stage' => SubmissionStageEnum::RT,
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
        if(auth()->user()->role !== UserRoleEnum::CIVILIAN) {
            $stage = 0;

            if(auth()->user()->role === UserRoleEnum::RT) {
                $stage = SubmissionStageEnum::RT->value;
            } else if (auth()->user()->role === UserRoleEnum::RW) {
                $stage = SubmissionStageEnum::RW->value;
            } else if (auth()->user()->role === UserRoleEnum::LURAH) {
                $stage = SubmissionStageEnum::LURAH->value;
            }

            $builder = DocumentSubmission::where('stage', $stage)->search();
        } else {
            $builder = DocumentSubmission::where('user_id', auth()->user()->id)->search();
        }

        $documentSubmission = $builder->search();

        $result = [
            'count' => $documentSubmission->count(),
            'document_submissions' => $documentSubmission->getResult()->load('document_attachments'),
        ];

        return response()->api($result, 200, 'ok', 'Berhasil mendapatkan data pengajuan');
    }

    public function show(Request $request, DocumentSubmission $submission)
    {
        return response()->api($submission->load('document_attachments', 'document_progresses.user.staff'), 200, 'ok', 'Berhasil mendapatkan detil pengajuan');
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

    public function storeDocumentProgress(DocumentProgressRequest $request, DocumentSubmission $submission)
    {
        if($submission->stage === SubmissionStageEnum::RT && auth()->user()->role !== UserRoleEnum::RT) {
            return response()->api([], 400, 'error', 'Gagal melakukan approval dokumen, pengajuan ini masih ditahap ' . SubmissionStageEnum::getString($submission->stage));
        } else if ($submission->stage === SubmissionStageEnum::RW && auth()->user()->role !== UserRoleEnum::RW) {
            return response()->api([], 400, 'error', 'Gagal melakukan approval dokumen, pengajuan ini masih ditahap ' . SubmissionStageEnum::getString($submission->stage));
        } else if ($submission->stage === SubmissionStageEnum::LURAH && auth()->user()->role !== UserRoleEnum::LURAH) {
            return response()->api([], 400, 'error', 'Gagal melakukan approval dokumen, pengajuan ini masih ditahap ' . SubmissionStageEnum::getString($submission->stage));
        }

        $data = $request->validated();
        DB::beginTransaction();
        try {
            if($data['file']) {
                $data['file_path'] = Storage::disk('public')->put('submissions', $data['file']);
            }

            $documentProgress = DocumentProgress::create([
                ...$data,
                'document_submission_id' => $submission->id,
                'user_id' => auth()->user()->id,
            ]);

            if($data['status'] === ProgressStatusEnum::REVISE->value) {
                $submission->update(['status' => SubmissionStatusEnum::REVISE]);
            } else {
                $submission->update(['stage' => $submission->stage->value + 1]);
            }

            DB::commit();
            return response()->api($documentProgress->load('user.staff'), 200, 'ok', 'Berhasil melakukan approval dokumen');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal melakukan approval dokumen');
        }
    }
}
