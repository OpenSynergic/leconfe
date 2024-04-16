<x-website::layouts.main>
    <div class="space-y-2">
        <section id="highlight-conference" class="p-5 space-y-4">
            <div class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 gap-4">
                <div class="flex flex-col gap-4 flex-1">
                    <div>
                        <img src="https://placehold.co/960x200" class="w-full" alt="">
                    </div>
                    <div class="inline-flex items-center space-x-2">
                        <h1 class="cf-name text-2xl">International Conference of MATHEMATICS AND ITS Application
                            Artificial Intelligent on ChatGPT and the Effect</h1>
                        <span
                            class="badge bg-purple-400 text-white rounded-full px-3 text-xs flex items-center justify-center h-8">{{ $currentConference->type }}</span>
                    </div>
                    @if ($currentConference->getMeta('description'))
                        <div class="user-content">
                            {{ $currentConference->getMeta('description') }}
                        </div>
                    @endif
                    @if ($currentConference->series->where('active', true)->first()->description)
                        <div>
                            <h2 class="text-base font-medium">Series Description :</h2>
                            <div class="user-content">
                                {{ $currentConference->series->where('active', true)->first()->description }}
                            </div>
                        </div>
                    @endif
                    @if ($topics->isNotEmpty())
                        <div>
                            <h2 class="text-base font-medium mb-1">Topics :</h2>
                            <div class="flex flex-wrap w-full gap-2">
                                @foreach ($topics as $topic)
                                    <span
                                        class="badge badge-outline text-xs border border-gray-300 h-6 text-secondary">{{ $topic->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div>
                        <a href="#"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 w-fit">
                            <x-heroicon-o-document-arrow-up class="h-5 w-5 mr-2" />
                            Submit Now
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="conference-partner" class="p-5 space-y-4">
            @if ($currentConference->sponsors)
                <div class="sponsors space-y-4" x-data="carousel">
                    <h2 class="text-xl text-center">Conference Partner</h2>
                    <div class="sponsors-carousel flex items-center w-full gap-4" x-bind="carousel">
                        <button x-on:click="toLeft"
                            class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                            <x-heroicon-m-chevron-left class="h-6 w-fit text-white" />
                        </button>
                        <ul x-ref="slider"
                            class="flex-1 flex w-full snap-x snap-mandatory overflow-x-scroll gap-3 py-4">
                            @foreach ($currentConference->sponsors as $sponsor)
                                <li @class([
                                    'flex shrink-0 snap-start flex-col items-center justify-center',
                                    'ml-auto' => $loop->first,
                                    'mr-auto' => $loop->last,
                                ])>
                                    <img class="max-h-24 w-fit"
                                        src="{{ $sponsor->getFirstMedia('logo')?->getAvailableUrl(['thumb']) }}"
                                        alt="{{ $sponsor->name }}">
                                </li>
                            @endforeach
                        </ul>
                        <button x-on:click="toRight"
                            class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                            <x-heroicon-m-chevron-right class="h-6 w-fit text-white" />
                        </button>
                    </div>
                </div>
            @endif
        </section>
        <section id="conference-detail-tab" class="p-5 space-y-4">
            <div x-data="{ activeTab: 'information' }" class=" bg-white">
                <div class="border border-t-0 border-x-0 border-gray-300">
                    <button @click="activeTab = 'information'"
                        :class="{ 'text-blue-400 bg-white': activeTab === 'information', 'bg-gray-100': activeTab !== 'information' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300"
                        style="margin-bottom: -1px">Information</button>
                    <button @click="activeTab = 'participant-info'"
                        :class="{ 'text-primary bg-white': activeTab === 'participant-info', 'bg-gray-100': activeTab !== 'participant-info' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300"
                        style="margin-bottom: -1px">Participant Info</button>
                    <button @click="activeTab = 'registration-info'"
                        :class="{ 'text-primary bg-white': activeTab === 'registration-info', 'bg-gray-100': activeTab !== 'registration-info' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300"
                        style="margin-bottom: -1px">Registration Info</button>
                    <button @click="activeTab = 'contact-info'"
                        :class="{ 'text-primary bg-white': activeTab === 'contact-info', 'bg-gray-100': activeTab !== 'contact-info' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300"
                        style="margin-bottom: -1px">Contact Info</button>
                    <button @click="activeTab = 'editorial-committee'"
                        :class="{ 'text-primary bg-white': activeTab === 'editorial-committee', 'bg-gray-100': activeTab !== 'editorial-committee' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300"
                        style="margin-bottom: -1px">Editorial Committee</button>
                </div>
                <div x-show="activeTab === 'information'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="conference-information" class="flex flex-col gap-2">
                        <table class="w-full text-sm" cellpadding="4">
                            <tr>
                                <td width="80">Type</td>
                                <td width="20">:</td>
                                <td>{{ $currentConference->type }}</td>
                            </tr>
                            @if ($currentConference->hasMeta('location'))
                                <tr>
                                    <td>Place</td>
                                    <td>:</td>
                                    <td>{{ $currentConference->getMeta('location') }}</td>
                                </tr>
                            @endif
                            @if ($currentConference->date_start)
                                <tr>
                                    <td>Date</td>
                                    <td>:</td>
                                    <td>
                                        {{ date(setting('format.date'), strtotime($currentConference->date_start)) }} -
                                        {{ date(setting('format.date'), strtotime($currentConference->date_end)) }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </article>
                </div>

                <div x-show="activeTab === 'participant-info'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="participant-info">
                        <h1>Quota</h1>
                        <table class="text-sm border-separate border-spacing-2">
                            <tr>
                                <td>Paper</td>
                                <td>:</td>
                                <td>400 Papers</td>
                                <td>
                                    <span class="badge badge-primary text-xs h-6">355 Accepted</span>
                                </td>
                                <td><span class="badge badge-outline text-xs border h-6 text-primary">45
                                        Available</span></td>
                            </tr>
                            <tr>
                                <td>Participant</td>
                                <td>:</td>
                                <td>60 Seats</td>
                                <td>
                                    <span class="badge badge-primary text-xs h-6">30 Reserved</span>
                                </td>
                                <td><span class="badge badge-outline text-xs border h-6 text-primary">30
                                        Available</span></td>
                            </tr>
                        </table>
                    </article>
                </div>

                <div x-show="activeTab === 'registration-info'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="registration-info" class="overflow-x-auto">
                        <h1>Fee</h1>
                        <table class="table w-full border-collapse border border-gray-300 my-2">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        Jenis Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        Mahasiswa S1</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        Mahasiswa S2, S3, dan Guru</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                        Umum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Peserta Biasa</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp50.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp100.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp150.000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Poster</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp100.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp150.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp200.000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Pemakalah Oral</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp100.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp150.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp200.000</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex flex-row-reverse">
                            <a href="#"
                                class="bg-green-500 hover:bg-green-600 py-1 px-3 rounded-md text-sm text-white cursor-pointer">Register</a>
                        </div>
                        <div class="text-sm">
                            <h2>Payment Via Bank</h2>
                            <p>Bank Negara Indonesia (BNI), <span class="font-bold">No Rekening 1234567890 (Siminar
                                    Bersama 2023)</span></p>
                            <p class="text-red-500">Setelah mebayar silakan konfirmasi ke contact info, konfirmasi
                                pembayaran paling lambat 21 Maret 2024.</p>
                        </div>
                    </article>
                </div>
                <div x-show="activeTab === 'contact-info'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="contact-info">
                        <h1>Contact Person</h1>
                        <table class="text-sm">
                            <tr>
                                <td class="font-bold">Nama</td>
                                <td class="px-3">:</td>
                                <td>Ana</td>
                            </tr>
                            <tr>
                                <td class="font-bold">No HP/WhatsApp</td>
                                <td class="px-3">:</td>
                                <td>0812-3456-6122</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Email</td>
                                <td class="px-3">:</td>
                                <td>seminarbersama2023@gmail.com</td>
                            </tr>
                        </table>
                    </article>
                </div>
                <div x-show="activeTab === 'editorial-committee'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="editorial-committee">
                        <h1>Editorial</h1>
                        <div class="flex flex-col flex-start gap-y-4 my-2">
                            <div class="flex flex-row text-sm w-fit">
                                <img  src="https://placeholder.co/64x64" alt="editor-thumbnail"
                                    class="rounded-full w-16 h-16 m-auto block">
                                <div class="pl-4">
                                    <h3>Prof. David Bramhiers, Ph.D.</h3>
                                    <p class="text-blue-500">Lead Editor</p>
                                    <p class="text-secondary">Oxford University</p>
                                    <div class="flex flex-row items-center">
                                        <img class="w-4 h-4 mr-2" src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Google_Scholar_logo.svg" alt="">
                                        <a href="#" class="text-cyan-500 underline underline-offset-2">123847742</a>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row text-sm w-fit">
                                <img  src="https://placeholder.co/64x64" alt="editor-thumbnail"
                                    class="rounded-full w-16 h-16 m-auto block">
                                <div class="pl-4">
                                    <h3>Prof. David Bramhiers, Ph.D.</h3>
                                    <p class="text-blue-500">Lead Editor</p>
                                    <p class="text-secondary">Oxford University</p>
                                    <div class="flex flex-row items-center">
                                        <img class="w-4 h-4 mr-2" src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Google_Scholar_logo.svg" alt="">
                                        <a href="#" class="text-cyan-500 underline underline-offset-2">123847742</a>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row text-sm w-fit">
                                <img  src="https://placeholder.co/64x64" alt="editor-thumbnail"
                                    class="rounded-full w-16 h-16 m-auto block">
                                <div class="pl-4">
                                    <h3>Prof. David Bramhiers, Ph.D.</h3>
                                    <p class="text-blue-500">Lead Editor</p>
                                    <p class="text-secondary">Oxford University</p>
                                    <div class="flex flex-row items-center">
                                        <img class="w-4 h-4 mr-2" src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Google_Scholar_logo.svg" alt="">
                                        <a href="#" class="text-cyan-500 underline underline-offset-2">123847742</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>






        @if ($currentConference->date_start || $currentConference->hasMeta('location'))
            <section id="conference-information" class="p-5 flex flex-col gap-2">
                <h2 class="text-heading">Information</h2>
                <table class="w-full text-sm" cellpadding="4">
                    <tr>
                        <td width="80">Type</td>
                        <td width="20">:</td>
                        <td>{{ $currentConference->type }}</td>
                    </tr>
                    @if ($currentConference->hasMeta('location'))
                        <tr>
                            <td>Place</td>
                            <td>:</td>
                            <td>{{ $currentConference->getMeta('location') }}</td>
                        </tr>
                    @endif

                    @if ($currentConference->date_start)
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td>
                                {{ date(setting('format.date'), strtotime($currentConference->date_start)) }} -
                                {{ date(setting('format.date'), strtotime($currentConference->date_end)) }}
                            </td>
                        </tr>
                    @endif
                </table>
            </section>
        @endif

        @if ($participantPosition->isNotEmpty())
            <section id="conference-speakers" class="p-5 flex flex-col gap-2">
                <h2 class="text-heading">Speakers</h2>
                <div class="cf-speakers space-y-6">
                    @foreach ($participantPosition as $position)
                        @if ($position->participants->isNotEmpty())
                            <div class="space-y-4">
                                <h3 class="text-base">{{ $position->name }}</h3>
                                <div class="cf-speaker-list grid sm:grid-cols-2 gap-2">
                                    @foreach ($position->participants as $participant)
                                        <div class="cf-speaker h-full flex gap-2">
                                            <img class="w-16 h-16 object-cover aspect-square rounded-full"
                                                src="{{ $participant->getFilamentAvatarUrl() }}"
                                                alt="{{ $participant->fullName }}" />
                                            <div>
                                                <div class="speaker-name text-sm text-gray-900">
                                                    {{ $participant->fullName }}
                                                </div>
                                                <div class="speaker-meta">
                                                    @if ($participant->getMeta('expertise'))
                                                        <div class="speaker-expertise text-2xs text-primary">
                                                            {{ implode(', ', $participant->getMeta('expertise') ?? []) }}
                                                        </div>
                                                    @endif
                                                    @if ($participant->getMeta('affiliation'))
                                                        <div class="speaker-affiliation text-2xs text-secondary">
                                                            {{ $participant->getMeta('affiliation') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif


        @if ($currentConference->getMeta('additional_content'))
            <section class="user-content px-5">
                {!! $currentConference->getMeta('additional_content') !!}
            </section>
        @endif

        @if ($venues->isNotEmpty())
            <section class="venues p-5">
                <h2 class="text-heading">Venues</h2>
                <div class="venue-list space-y-3">
                    @foreach ($venues as $venue)
                        <div class="venue flex gap-3">
                            @if ($venue->hasMedia('thumbnail'))
                                <img class="max-w-[100px]"
                                    src="{{ $venue->getFirstMedia('thumbnail')->getAvailableUrl(['thumb', 'thumb-xl']) }}">
                            @endif
                            <div class="space-y-2">
                                <div>
                                    <a
                                        class="group/link relative inline-flex items-center justify-center outline-none gap-1 font-thin">
                                        <span
                                            class="font-semibold group-hover/link:underline group-focus-visible/link:underline text-base">
                                            {{ $venue->name }}
                                        </span>
                                    </a>
                                    <p class="text-gray-500 text-sm flex items-center gap-1"><x-heroicon-m-map-pin
                                            class="size-4" /> {{ $venue->location }}</p>
                                </div>
                                <p class="text-gray-500 text-xs">{{ $venue->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-website::layouts.main>
