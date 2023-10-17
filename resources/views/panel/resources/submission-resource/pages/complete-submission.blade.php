<x-filament::page>
    <div class="mx-auto max-w-xl w-full space-y-6">
        <h1 class="font-bold text-2xl text-center">Submission complete</h1>
        <x-filament::card>
          <p class="text-center">You have submitted your abstract, and an email has been sent to notify you. The manager will review your submission and send you another email once they are done.</p>
          <br/>
          <p class="text-center">
            Go to <a href="{{ App\Panel\Resources\SubmissionResource::getUrl('index') }}" class="text-primary-700">Submissions Page</a> to check the status of your submission.
          </p>
        </x-filament::card>
    </div>
</x-filament::page>
