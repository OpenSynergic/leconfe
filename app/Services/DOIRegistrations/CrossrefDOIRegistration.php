<?php

namespace App\Services\DOIRegistrations;

use App\Classes\ImportExport\ExportArticleCrossref;
use App\Models\Enums\DOIStatus;
use App\Models\Submission;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
class CrossrefDOIRegistration extends BaseDOIRegistration
{
	public function getName(): string
	{
		return 'Crossref';
	}

	public function getTableActions(): array
	{
		return [
			ActionGroup::make([
				Action::make('export')
					->icon('heroicon-s-document-arrow-down')
					->color('primary')
					->label('Export XML')
					->action(function (Submission $record) {
						try {
							$xml = $this->exportXml($record);
							$filename = Str::slug($record->getKey() . '-' . $record->getMeta('title')) . '.xml';

							return response()->streamDownload(function () use ($xml) {
								echo $xml;
							}, $filename);
						} catch (\Throwable $th) {
							Notification::make()
								->danger()
								->title('Failed to export')
								->body($th->getMessage())
								->send();
						}
					}),
				Action::make('deposit')
					->label('Deposit XML')
					->icon('heroicon-s-cloud-arrow-up')
					->color('primary')
					->action(function (Submission $record) {
						try {
							$result = $this->depositXml($record);

							if ($result) {
								Notification::make()
									->success()
									->title('Deposit success')
									->send();
							}
						} catch (\Exception $e) {
							Notification::make()
								->danger()
								->title('Failed to deposit')
								->body($e->getMessage())
								->send();
						}
					}),
				Action::make('view_error')
					->label('View Error Message')
					->color('danger')
					->icon('heroicon-o-x-mark')
					->hidden(fn(Submission $record) => $record->doi?->status !== DOIStatus::Error)
					->modalWidth(MaxWidth::Large)
					->modalSubmitAction(false)
					->modalCancelAction(false)
					->infolist(function (Infolist $infolist, Submission $record) {
						$doi = $record->doi;


						$infolist->state([
							'message' => $doi->getMeta('crossref_message'),
						]);

						$infolist->schema([
							TextEntry::make('message')
								->hiddenLabel()
								->formatStateUsing(function (?string $state) {
									return new HtmlString($state);
								}),
						]);

						return $infolist;
					})
			])
				->size(ActionSize::Small)
				->outlined()
				->label('Crossref')
				->button()
				->hidden(fn(Submission $record) => !$record->doi),
		];
	}

	public function getSettingFormSchema(): array
	{
		return [
			// Section::make('Automatic Deposit')
			// 	->schema([
			// 		Placeholder::make('doi_automatic_deposit_description')
			// 			->content("The DOI registration and metadata can be automatically deposited with the selected registration agency whenever an item with a DOI is published. Automatic deposit will happen at scheduled intervals and each DOI's registration status can be monitored from the DOI management page")
			// 			->hiddenLabel(),
			// 		Checkbox::make('meta.doi_automatic_deposit')
			// 			->label('Automatically deposit DOIs')
			// 	]),5
			Placeholder::make('Crossref Settings')
				->content("The following items are required for a successful Crossref deposit"),
			TextInput::make('meta.doi_crossref_depositor_name')
				->label("Depositor Name")
				->helperText("Name of the organization registering the DOIs. It is included with deposited metadata and used to record who submitted the deposit.")
				->required(),
			TextInput::make('meta.doi_crossref_depositor_email')
				->label("Depositor Email")
				->helperText("Email address of the individual responsible for registering content with Crossref. It is included with the deposited metadata and used when sending the deposit confirmation email.")
				->required(),
			Placeholder::make('information')
				->hiddenLabel()
				->content(new HtmlString(<<<HTML
					<div class="prose prose-sm max-w-none">
						<p>If you would like to register Digital Object Identifiers (DOIs) directly with <a href="http://www.crossref.org/">Crossref</a>, you will need to add your <a href="https://www.crossref.org/documentation/member-setup/account-credentials/">Crossref account credentials</a> into the username and password fields below.</p>
						<p>Depending on your Crossref membership, there are two ways to enter your username and password:</p>
						<ul>
							<li>If you are using an organizational account, add your <a href="https://www.crossref.org/documentation/member-setup/account-credentials/#00376">shared username and password</a></li>
							<li>If you are using a <a href="https://www.crossref.org/documentation/member-setup/account-credentials/#00368">personal account</a>, enter your email address and the role in the username field. The username will look like: email@example.com/role</li>
							<li>If you do not know or have access to your Crossref credentials, you can contact <a href="https://support.crossref.org/">Crossref support</a> for assistance. Without credentials, you may still export metadata into the Crossref XML format, but you cannot register your DOIs with Crossref from Leconfe.</li>
						</ul>
					</div>
				HTML)),
			TextInput::make('meta.doi_crossref_username')
				->label("Username")
				->helperText('The Crossref username that will be used to authenticate your deposits. If you are using a personal account, please see the advice above.'),
			TextInput::make('meta.doi_crossref_password')
				->label("Password")
				->password()
				->revealable()
				->required(),
			Checkbox::make('meta.doi_crossref_test')
				->label('Use the Crossref test API (testing environment) for the DOI deposit.')
				->inline()
		];
	}

	public function exportXml(Submission $submission)
	{
		$export = new ExportArticleCrossref($submission);
		return $export->exportXml();
	}

	public function depositXml(Submission $submission)
	{
		$export = new ExportArticleCrossref($submission);
		return $export->depositXml();
	}
}
