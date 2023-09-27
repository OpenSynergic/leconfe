<?php

namespace App\Administration\Livewire;

use App\Infolists\Components\BladeEntry;
use App\Infolists\Components\VerticalTabs;
use App\Mail\Templates\VerifyUserEmail;
use App\Models\MailTemplate;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class EmailSetting extends Component implements HasInfolists, HasForms, HasTable
{
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithTable;

    public function mount()
    {
        // MailTemplate::create([
        //     'mailable' => VerifyUserEmail::class,
        //     'subject' => 'Verify Email Address',
        //     'html_template' => 'Please click the button below to verify your email address. <a href="{{ verificationUrl }}">Verify Email Address</a>. <br> If you did not create an account, no further action is required.',
        //     'text_template' => null,
        // ]);
        // $user = auth()->user();
        // // event(new \Illuminate\Auth\Events\Registered($user));
        // Mail::to($user->email)->send(new VerifyUserEmail($user));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MailTemplate::query())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('subject')
                            ->searchable()
                            ->weight(FontWeight::Medium)
                            ->sortable(),
                        TextColumn::make('description'),
                        TextColumn::make('mailable')
                            ->badge()
                            ->color('primary'),
                    ]),
                ]),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        // Placeholder::make('description')
                        //     ->label(''),
                        TextInput::make('subject')
                            ->required()
                            ->rules('required'),
                        TinyEditor::make('html_template')
                            ->label('Body')
                            ->minHeight(500)
                            ->required()
                            // ->simple()
                            ->rules('required'),
                    ])
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                VerticalTabs\Tabs::make()
                    ->schema([
                        VerticalTabs\Tab::make('Setup')
                            ->icon('heroicon-o-cog'),
                        VerticalTabs\Tab::make('Email Templates')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                BladeEntry::make('email-templates')
                                    ->blade('{{ $this->table }}')
                            ]),

                    ])
                    ->activeTab(2)
            ]);
    }


    public function render()
    {
        return view('administration.livewire.email-setting');
    }
}
