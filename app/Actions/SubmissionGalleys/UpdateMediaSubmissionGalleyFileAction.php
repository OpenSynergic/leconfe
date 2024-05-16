<?php

namespace App\Actions\SubmissionGalleys;

use App\Constants\SubmissionFileCategory;
use App\Models\SubmissionGalley;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMediaSubmissionGalleyFileAction
{
    use AsAction;

    public function handle(SubmissionGalley $submissionGalley, $fileUpload, $type = null)
    {
        try {
            DB::beginTransaction();

            if ($media = $submissionGalley->media()->where('uuid', reset($fileUpload))->first()) {
                $checkSubmissionFile = $submissionGalley->file 
                ? $submissionGalley->file->update(['media_id' => $media->id, 'submission_file_type_id' => $type])
                : $submissionGalley->submission->submissionFiles()->create([
                    'media_id' => $media->id,
                    'submission_file_type_id' => $type,
                    'category' => SubmissionFileCategory::GALLEY_FILES,
                ]);

                $submissionGalley->update([
                    'submission_file_id' => $submissionGalley->file->id ?? $checkSubmissionFile->id,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
