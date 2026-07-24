<?php

namespace App\Actions\SubmissionGalleys;

use App\Constants\SubmissionFileCategory;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionGalley;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CreateSubmissionGalleyAction
{
    use AsAction;

    public function handle(Submission $submission, array $data, ?SpatieMediaLibraryFileUpload $componentMedia): SubmissionGalley
    {
        $isRemoteUrl = (bool) data_get($data, 'is_remote_url', false);
        $media = data_get($data, 'media');
        $temporaryFile = null;

        if (! $isRemoteUrl) {
            if (! $componentMedia) {
                throw new InvalidArgumentException(
                    'A SpatieMediaLibraryFileUpload component is required when creating a local submission galley.'
                );
            }

            if (! is_array($media) || blank(data_get($media, 'type'))) {
                throw new InvalidArgumentException(
                    'Media data and a file type are required when creating a local submission galley.'
                );
            }

            $temporaryFileUpload = $componentMedia->getState();
            $temporaryFile = $temporaryFileUpload instanceof TemporaryUploadedFile
                ? $temporaryFileUpload
                : (is_array($temporaryFileUpload) ? reset($temporaryFileUpload) : null);

            if (! $temporaryFile instanceof TemporaryUploadedFile) {
                throw new InvalidArgumentException(
                    'A temporary uploaded file is required when creating a local submission galley.'
                );
            }
        }

        try {
            DB::beginTransaction();

            $submissionGalley = $submission->galleys()->create($data);

            if (! $isRemoteUrl) {
                $fileName = data_get($data, 'media.name') ?? null;
                $saveGalleyMedia = $this->saveUploadedMedia($submissionGalley, $temporaryFile, $componentMedia, $fileName);

                $files = SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'media_id' => $saveGalleyMedia->id,
                    'submission_file_type_id' => $media['type'],
                    'category' => SubmissionFileCategory::GALLEY_FILES,
                ]);

                $submissionGalley->update([
                    'submission_file_id' => $files->id,
                ]);
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $submissionGalley;
    }

    private function saveUploadedMedia(SubmissionGalley $record, TemporaryUploadedFile $file, SpatieMediaLibraryFileUpload $component, ?string $customFileName = null)
    {
        $mediaAdder = $record->addMediaFromString($file->get());

        $fileExtension = $file->getClientOriginalExtension();
        $filename = $customFileName ? $customFileName.'.'.$fileExtension : $component->getUploadedFileNameForStorage($file);

        $media = $mediaAdder
            ->addCustomHeaders($component->getCustomHeaders())
            ->usingFileName($filename)
            ->usingName($customFileName ?? pathinfo(SpatieMediaLibraryFileUpload::getClientOriginalName($file), PATHINFO_FILENAME))
            ->storingConversionsOnDisk($component->getConversionsDisk() ?? '')
            ->withCustomProperties($component->getCustomProperties($file))
            ->withManipulations($component->getManipulations())
            ->withResponsiveImagesIf($component->hasResponsiveImages())
            ->withProperties($component->getProperties())
            ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName());

        return $media;
    }
}
