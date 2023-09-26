<x-website::layouts.main>

    <div class="card px-5 py-4">
        <div class="card-body space-y-2 bg-gray-50 border">
            <div class="cf-current space-y-1 p-4">
                <div class="flex justify-between">
                    <div class="card-title font-normal">{{ $currentConference->name }}</div>
                </div>

                @if ($currentConference->hasMeta('date_held'))
                    <div class="pb-2">
                        <div class="inline-flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            <span class="font-normal text-xs">{{ $currentConference->getMeta('date_held') }}</span>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col gap-2 sm:flex-row">
                    @if ($currentConference->hasMedia('thumbnail'))
                        <div class="cf-thumbnail w-auto sm:max-w-[12rem]">
                            <img class="h-full w-full rounded object-contain"
                                src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}" alt="" />
                        </div>
                    @endif

                    <div class="flex flex-col gap-2">
                        @if ($currentConference->hasMeta('description'))
                            <div class="cf-description h-full w-full">
                                <p class="text-[.95em]">{!! $currentConference->getMeta('description') !!}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card ps-8">
        <div class="card-body space-y-2">
            <div class="cf-information">
                <h4 class="card-title pb-3 text-center font-normal">Information</h4>
                @if ($currentConference->hasMeta('date_held') || $currentConference->hasMeta('location'))
                    <table class="w-full text-[.95em] md:text-base">
                        <tr>
                            <td width="80">Type</td>
                            <td width="20">:</td>
                            <td>{{ $currentConference->type }}</td>
                        </tr>
                        <tr>
                            <td>Place</td>
                            <td>:</td>
                            <td>{{ $currentConference->getMeta('location') }}</td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td>{{ $currentConference->getMeta('date_held') }}</td>
                        </tr>
                    </table>
                @else
                    <div class="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="h-6 w-6 shrink-0 stroke-info">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Data Not Available.</span>
                    </div>
                @endif
            </div>

            <div class="cf-quota">
                <h4 class="card-title pb-2 text-center font-normal">Quota</h4>
                <table class="w-full text-[.95em] md:text-base">
                    <tr>
                        <td width="80">Papers</td>
                        <td width="20">:</td>
                        <td width="90">
                            <span>400 Papers</span>
                        </td>
                        <td width="10">
                            <span class="w-30 badge h-5 w-24 px-2 py-2 text-[0.65em]">400 Accepted</span>
                        </td>
                        <td>
                            <span class="badge badge-primary badge-outline h-5 w-24 px-2 py-2 text-[0.65em]">40
                                Accepted</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Participan</td>
                        <td>:</td>
                        <td>
                            <span>60 Seats</span>
                        </td>
                        <td>
                            <span class="badge h-5 w-24 px-2 py-2 text-[0.65em]">300 Accepted</span>
                        </td>
                        <td>
                            <span class="badge badge-primary badge-outline h-5 w-24 px-2 py-2 text-[0.65em]">30
                                Available</span>
                        </td>
                    </tr>
                </table>
                {{-- <div class="alert bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="h-6 w-6 shrink-0 stroke-info">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-500">To be announced</span>
                    </div> --}}
            </div>

            <div class="cf-contact">
                <h4 class="card-title pb-3 text-center font-normal">Contact</h4>
                <table class="w-full text-[.95em] md:text-base">
                    <tr>
                        <td width="80">Email</td>
                        <td>:</td>
                        <td>SMKN 4 Banjarmasin, Indonesia</td>
                    </tr>
                    <tr>
                        <td>Website</td>
                        <td>:</td>
                        <td><a href="#" class="hover:text-blue-600">https:/www.wildcat.arizona.com</a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="h-4 w-4">
                                    <path fill-rule="evenodd"
                                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Kahfi</span>
                            </div>
                        </td>
                        <td>:</td>
                        <td><a href="#" class="hover:text-blue-600">kahfi@gmail.com</a></td>
                    </tr>

                    <tr>
                        <td>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="h-4 w-4">
                                    <path fill-rule="evenodd"
                                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Dicky</span>
                            </div>
                        </td>
                        <td width="20">:</td>
                        <td><a href="#" class="hover:text-blue-600">dicky@gmail.com</a></td>
                    </tr>
                </table>
                {{-- <div class="alert bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="h-6 w-6 shrink-0 stroke-info">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-500">To be announced</span>
                    </div> --}}
            </div>
        </div>
    </div>

    <div class="card ms-5 w-[510px] lg:w-[60%]">
        <div class="card-body space-y-2">
            <h4 class="card-title text-center font-normal">Keynote Speakers</h4>

            <div class="cf-speakers">
                <div class="flex flex-wrap gap-9  md:ms-3">
                    <div class="flex flex-col space-y-4">
                        <p class="text-[15px]">Opening Speakers</p>
                        <div class="flex items-center space-x-2">
                            <div class="avatar">
                                <div
                                    class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                    <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                        alt="" />
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-[0.75em]">Prof. Ashton Faisal, Ph.D</p>
                                <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                                <small class="text-[0.75em] text-gray-400">University, Japan</small>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-4">
                        <p class="text-[15px]">Closing Speakers</p>
                        <div class="me-10 flex items-center space-x-2">
                            <div class="avatar">
                                <div
                                    class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                    <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                        alt="" />
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-[0.75em]">Prof. Ashton Ganjar Ph.D</p>
                                <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                                <small class="text-[0.75em] text-gray-400">University, Japan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-2  md:ms-3">
                <div class="max-w-[200px]">
                    <p class="text-[15px]">On Stage Speakers</p>
                </div>
                <div class="flex flex-wrap space-y-4  md:justify-start">

                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[0.75em]">Prof. Ashton, Ph.D</p>
                            <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                            <small class="text-[0.75em] text-gray-400">University, Japan</small>
                        </div>
                    </div>

                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[0.75em]">Prof. Ashton, Ph.D</p>
                            <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                            <small class="text-[0.75em] text-gray-400">University, Japan</small>
                        </div>
                    </div>

                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[0.75em]">Prof. Ashton, Ph.D</p>
                            <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                            <small class="text-[0.75em] text-gray-400">University, Japan</small>
                        </div>
                    </div>
                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[0.75em]">Prof. Ashton, Ph.D</p>
                            <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                            <small class="text-[0.75em] text-gray-400">University, Japan</small>
                        </div>
                    </div>
                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[0.75em]">Prof. Ashton, Ph.D</p>
                            <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                            <small class="text-[0.75em] text-gray-400">University, Japan</small>
                        </div>
                    </div>

                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[0.75em]">Prof. Ashton, Ph.D</p>
                            <small class="text-[0.75em] text-blue-400">"Technology Quantum"</small>
                            <small class="text-[0.75em] text-gray-400">University, Japan</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="card mt-4 px-5">
        <div class="card-body">
            <div class="card-title font-normal">Registration Fee</div>
            <div
                class="grid grid-cols-3 items-center justify-center gap-6 border border-gray-300 bg-gray-50 p-4 text-center">
                <div class="grid grid-flow-row space-y-2 text-start">
                    <h2 class="font-semibold">Author Cluster</h2>
                    <p>International Author</p>
                    <p>Domestic Author</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                    <h2 class="font-semibold">Fee</h2>
                    <p>USD 125</p>
                    <p>IDR 1.750.000</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                    <h2 class="font-semibold">Early Bid Registration</h2>
                    <p>USD 75</p>
                    <p>IDR.1000.000</p>
                </div>

                <div class="grid grid-flow-row space-y-2 text-start">
                    <h2 class="font-semibold">Participan Cluster</h2>
                    <p>Internation Cluster</p>
                    <p>Domestic Participant</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                    <h2 class="font-semibold">Fee</h2>
                    <p>USD 30</p>
                    <p>IDR.300.000</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                    <h2 class="font-semibold">Early Bid Registration</h2>
                    <p>USD 25</p>
                    <p>IDR.250.000</p>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <small class="text-gray-500">Register first to make any payment</small>
            </div>

            <div class="mt-7 flex justify-between gap-2 border border-gray-300 bg-gray-50">
                <div class="flex h-32 w-44 items-center justify-center shadow-sm">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">50</span>
                        <span class="font-extralight text-gray-600">Topics</span>
                    </div>
                </div>

                <div class="flex h-32 w-44 items-center justify-center">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">30</span>
                        <span class="font-extralight text-gray-600">Papers</span>
                    </div>
                </div>

                <div class="flex h-32 w-44 items-center justify-center">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">250</span>
                        <span class="font-extralight text-gray-600">Verificators</span>
                    </div>
                </div>

                <div class="flex h-32 w-44 items-center justify-center">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">30</span>
                        <span class="font-extralight text-gray-600">Country</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="card px-5">
        <div class="card-body">
            <!-- component -->
            <section class="body-font text-gray-600">
                <div class="max-w-7x1 container mx-auto py-16">
                    <div class="mb-4 flex w-full flex-wrap p-4">
                        <div class="mb-6 w-full lg:mb-0">
                            <h1 class="card-title mb-2 font-normal">Additional Content</h1>
                            <div class="h-[2px] w-20 rounded bg-primary"></div>
                        </div>
                    </div>
                    <div class="-m-4 flex flex-wrap">
                        <div class="p-4 md:w-1/2 xl:w-1/3">
                            <div class="rounded-lg bg-white p-6">
                                <img class="xs:h-72 mb-6 h-72 w-full rounded object-cover object-center sm:h-72 md:h-64 lg:h-60 xl:h-56"
                                    src="https://kuyou.id/content/images/ctc_2020021605150668915.jpg"
                                    alt="Image Size 720x400" />
                                <h3 class="title-font text-xs font-medium tracking-widest text-primary">SUBTITLE</h3>
                                <h2 class="title-font mb-4 text-lg font-medium text-gray-900">Chichen Itza</h2>
                                <p class="text-base leading-relaxed">Fingerstache flexitarian street art 8-bit
                                    waistcoat. Distillery hexagon disrupt edison bulbche.</p>
                            </div>
                        </div>
                        <div class="p-4 md:w-1/2 xl:w-1/3">
                            <div class="rounded-lg bg-white p-6">
                                <img class="xs:h-72 mb-6 h-72 w-full rounded object-cover object-center sm:h-72 md:h-64 lg:h-60 xl:h-56"
                                    src="https://asset.kompas.com/crops/Pk_pN6vllxXy1RshYsEv74Q1BYA=/56x0:1553x998/750x500/data/photo/2021/06/16/60c8f9d68ff4a.jpg"
                                    alt="Image Size 720x400" />
                                <h3 class="title-font text-xs font-medium tracking-widest text-primary">SUBTITLE</h3>
                                <h2 class="title-font mb-4 text-lg font-medium text-gray-900">Colosseum Roma</h2>
                                <p class="text-base leading-relaxed">Fingerstache flexitarian street art 8-bit
                                    waistcoat. Distillery hexagon disrupt edison bulbche.</p>
                            </div>
                        </div>
                        <div class="p-4 md:w-1/2 xl:w-1/3">
                            <div class="rounded-lg bg-white p-6">
                                <img class="xs:h-72 mb-6 h-72 w-full rounded object-cover object-center sm:h-72 md:h-64 lg:h-60 xl:h-56"
                                    src="https://images.immediate.co.uk/production/volatile/sites/7/2019/07/33-GettyImages-154260931-216706f.jpg?quality=90&resize=768%2C574"
                                    alt="Image Size 720x400" />
                                <h3 class="title-font text-xs font-medium tracking-widest text-primary">SUBTITLE</h3>
                                <h2 class="title-font mb-4 text-lg font-medium text-gray-900">Great Pyramid of Giza
                                </h2>
                                <p class="text-base leading-relaxed">Fingerstache flexitarian street art 8-bit
                                    waistcoat. Distillery hexagon disrupt edison bulbche.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-body">
            <div class="flex flex-col gap-4">
                <p class="mb-5 text-center text-2xl font-light text-gray-700">Sponsored By</p>
                <div class="flex flex-wrap justify-evenly gap-2">
                    <div class="avatar">
                        <div class="h-20 w-20 rounded-full">
                            <img src="https://tp.ugm.ac.id/wp-content/uploads/sites/1404/2019/04/logo-white.png"
                                alt="" />
                        </div>
                    </div>

                    <div class="avatar">
                        <div class="h-20 w-20 rounded-full">
                            <img src="https://ncsc.publiccharters.org/sites/default/files/2022-01/napcs-con-logo.png"
                                alt="" />
                        </div>
                    </div>

                    <div class="avatar">
                        <div class="h-20 w-20">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQXfxLAZBlbVRN8VKAmCc3ZytBeK5rJwAk-qw&usqp=CAU"
                                alt="" />
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="h-20 w-20 rounded-full">
                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/e/e4/University_of_Arizona_seal.svg/1200px-University_of_Arizona_seal.svg.png"
                                alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-conference::layouts.main>
