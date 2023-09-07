<x-conference::layouts.main>

    @foreach ($announcements as $announcement)
        <div class="card">
            <div class="card-body">
                <h2 class="card-title font-normal">Announcements</h2>
                <div class="flex flex-col space-y-2 rounded-sm border p-4">
                    <div class="flex flex-col gap-2">
                        <h5 class="text-md font-medium">{{ $announcement->title }}</h5>
                        <p class="text-[.65em]">
                            {!! $announcement->announcement !!}
                        </p>
                    </div>
                    <div class="flex justify-end">
                        <div class="inline-flex gap-2 rounded-sm text-xs shadow-sm" role="group">
                            <button
                                class="btn btn-primary btn-sm text-xs font-normal normal-case rounded-md text-white">1</button>
                            <button
                                class="btn btn-primary btn-sm text-xs font-normal normal-case rounded-md btn-outline">2</button>
                            <button
                                class="btn btn-primary btn-outline btn-sm text-xs font-normal normal-case rounded-md">3</button>
                            <button class="btn btn-primary btn-sm text-xs font-normal normal-case text-white">Read
                                More...</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="card-body space-y-2">
        <div class="conference-current space-y-4">
            <div class="flex justify-between">
                <div class="card-title font-normal">Current Conference</div>
                <div class="bg-gray-300 text-gray-800 badge badge-sm">{{ $currentConference->type }}</div>
            </div>
            <div class="pb-4">
                <span class="badge badge-outline">{{ $currentConference->getMeta('date_held') }}</span>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                @if ($currentConference->hasMedia('thumbnail'))
                    <div class="cf-thumbnail max-w-full lg:max-w-[12rem]">
                        <img class="w-full" src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}"
                            alt="{{ $currentConference->name }}">
                    </div>
                @endif
                <div class="cf-information space-y-2 w-full">
                    <div class="flex flex-wrap justify-between items-center -mt-1">
                        <h3 class="text-lg">{{ $currentConference->name }}</h3>
                    </div>
                    <div class="cf-description prose text-[.85em]">
                        @if ($currentConference->getMeta('location'))
                            <p>{{ $currentConference->getMeta('location') }}
                        @endif
                        {!! $currentConference->getMeta('description') !!}
                        <div class="flex justify-end flex-wrap">
                            <button
                                class="btn btn-primary btn-sm btn-primary text-white font-normal rounded-md">Enroll</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    </div>

    <div class="card">
        <div class="card-body space-y-2">
            <div class="information">
                <h4 class="card-title text-center font-normal pb-3">Information</h4>
                <table width="100%">
                    <tr>
                        <td width="20%">Type</td>
                        <td width="2%">:</td>
                        <td widhth="60%">{{ $currentConference->type ?? '' }}</td>
                    </tr>
                    <tr>
                        <td width="10%">Place</td>
                        <td width="2%">:</td>
                        <td widhth="60%">{{ $currentConference->getMeta('location') }}</td>
                    </tr>
                    @if ($currentConference->hasMeta('date_held'))
                        <tr>
                            <td width="10%">Date</td>
                            <td width="2%">:</td>
                            <td widhth="60%">{{ $currentConference->getMeta('date_held') }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="quota">
                <h4 class="card-title text-center font-normal pb-3">Quota</h4>
                <table width="100%">
                    <tr>
                        <td width="10%">Papers</td>
                        <td width="2%">:</td>
                        <td width="15%">
                            <span>400 Papers</span>
                        </td>
                        <td width="15%">
                            <span class="badge text-[0.65em] w-30 px-2 py-2 h-5 w-24">400 Accepted</span>
                        </td>
                        <td>
                            <span class="badge badge-outline badge-primary text-[0.65em] px-2 py-2 h-5 w-24">40
                                Accepted</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">Participan</td>
                        <td width="2%">:</td>
                        <td width="20%">
                            <span>60 Seats</span>
                        </td>
                        <td width="21%">
                            <span class="badge text-[0.65em] px-2 py-2 h-5 w-24">300 Accepted</span>
                        </td>
                        <td>
                            <span class="badge badge-outline badge-primary text-[0.65em] px-2 py-2 h-5 w-24">30
                                Available</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="contact">
                <h4 class="card-title text-center font-normal pb-3">Contact</h4>
                <table width="100%">
                    <tr>
                        <td width="16%">Mail Address</td>
                        <td width="2%">:</td>
                        <td width="60%">The University of Arizona, Tucson, AZ 85721</td>
                    </tr>
                    <tr>
                        <td width="10%">Website</td>
                        <td width="2%">:</td>
                        <td widhth="60%"><a href="#"
                                class="hover:text-blue-600">https:/www.wildcat.arizona.com</a></td>
                    </tr>
                    <tr>
                        <td width="10%">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-4 h-4">
                                    <path fill-rule="evenodd"
                                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Kahfi</span>

                            </div>
                        </td>
                        <td width="2%">:</td>
                        <td width="60%"><a href="#" class="hover:text-blue-600">kahfi@gmail.com</a></td>
                    </tr>

                    <tr>
                        <td width="10%">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-4 h-4">
                                    <path fill-rule="evenodd"
                                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Dicky</span>
                            </div>
                        </td>
                        <td width="2%">:</td>
                        <td width="60%"><a href="#" class="hover:text-blue-600">dicky@gmail.com</a></td>
                    </tr>
                </table>
            </div>
            <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
        </div>
    </div>
    <div class="card">
        <div class="card-body space-y-2">
            <h4 class="card-title text-center font-normal">Keynote Speakers</h4>
            <div class="flex justify-between p-4">
                <div class="flex flex-col space-y-4">
                    <p class="text-[15px]">Opening Speakers</p>
                    <div class="flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2">
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

                <div class="flex flex-col space-y-4">
                    <p class="text-[15px]">Closing Speakers</p>
                    <div class="flex items-center space-x-2 me-10">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2">
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

            <p class="mx-4 text-[15px]">On Stage Speakers</p>
            <div class="on-stage-speakers">
                <div class="grid grid-cols-2 px-4 md:grid-cols-3">
                    <div class="grid grid-flow-col items-center space-x-1 space-y-1">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full">
                                <img src="https://www.hbs.edu/Style%20Library/api/headshot.aspx?id=6532"
                                    alt="" />
                            </div>
                        </div>
                        <div class="me-10 grid w-full grid-flow-row">
                            <p class="text-[.75em]">Prof Asthon, Phd</p>
                            <small class="text-[.65em] text-blue-400">"Nano Technology Japan"</small>
                            <small class="text-[.65em] text-gray-400">University Japan</small>
                        </div>
                    </div>

                    <div class="grid grid-flow-col items-center space-x-1">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full">
                                <img src="https://www.hbs.edu/Style%20Library/api/headshot.aspx?id=6532"
                                    alt="" />
                            </div>
                        </div>
                        <div class="me-14 grid w-full grid-flow-row">
                            <p class="text-[.75em]">Prof Asthon, Phd</p>
                            <small class="text-[.65em] text-blue-400">"Nano Technology Japan"</small>
                            <small class="text-[.65em] text-gray-400">University Japan</small>
                        </div>
                    </div>

                    <div class="grid grid-flow-col items-center space-x-1">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full">
                                <img src="https://www.hbs.edu/Style%20Library/api/headshot.aspx?id=6532"
                                    alt="" />
                            </div>
                        </div>
                        <div class="me-14 grid w-full grid-flow-row">
                            <p class="text-[.75em]">Prof Asthon, Phd</p>
                            <small class="text-[.65em] text-blue-400">"Nano Technology Japan"</small>
                            <small class="text-[.65em] text-gray-400">University Japan</small>
                        </div>
                    </div>

                    <div class="grid grid-flow-col items-center space-x-1">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full">
                                <img src="https://www.hbs.edu/Style%20Library/api/headshot.aspx?id=6532"
                                    alt="" />
                            </div>
                        </div>
                        <div class="me-12 grid w-full grid-flow-row">
                            <p class="text-[.75em]">Prof Asthon, Phd</p>
                            <small class="text-[.65em] text-blue-400">"Nano Technology Japan"</small>
                            <small class="text-[.65em] text-gray-400">University Japan</small>
                        </div>
                    </div>

                    <div class="grid grid-flow-col items-center space-x-1">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full">
                                <img src="https://www.hbs.edu/Style%20Library/api/headshot.aspx?id=6532"
                                    alt="" />
                            </div>
                        </div>
                        <div class="grid w-full grid-flow-row">
                            <p class="text-[.75em]">Prof Asthon, Phd</p>
                            <small class="text-[.65em] text-blue-400">"Nano Technology Japan"</small>
                            <small class="text-[.65em] text-gray-400">University Japan</small>
                        </div>
                    </div>

                    <div class="grid grid-flow-col items-center space-x-1">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full">
                                <img src="https://www.hbs.edu/Style%20Library/api/headshot.aspx?id=6532"
                                    alt="" />
                            </div>
                        </div>
                        <div class="me-14 grid w-full grid-flow-row">
                            <p class="text-[.75em]">Prof Asthon, Phd</p>
                            <small class="text-[.65em] text-blue-400">"Nano Technology Japan"</small>
                            <small class="text-[.65em] text-gray-400">University Japan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title font-normal">Registration Fee</div>
            <div
                class="grid grid-cols-3 items-center justify-center gap-6 border bg-gray-50 border-gray-300 p-4 text-center">
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

            <div class="flex justify-end mt-4">
                <small class="text-gray-500">Register first to make any payment</small>
            </div>

            <div class="mt-7 flex gap-2 justify-center">
                <div class="flex h-32 w-44 items-center justify-center border border-gray-300 bg-gray-100 shadow-sm">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">50</span>
                        <span class="font-thin text-gray-600">Topics</span>
                    </div>
                </div>

                <div class="flex h-32 w-44 items-center justify-center border border-gray-300 bg-gray-100 shadow-sm">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">30</span>
                        <span class="font-thin text-gray-600">Papers</span>
                    </div>
                </div>

                <div class="flex h-32 w-44 items-center justify-center border border-gray-300 bg-gray-100 shadow-sm">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">250</span>
                        <span class="font-thin text-gray-600">Verificators</span>
                    </div>
                </div>

                <div class="flex h-32 w-44 items-center justify-center border border-gray-300 bg-gray-100 shadow-sm">
                    <div class="flex flex-col text-center">
                        <span class="text-3xl text-gray-600">30</span>
                        <span class="font-thin text-gray-600">Country</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex flex-col gap-4">
                <p class="text-2xl font-light text-center text-gray-700 mb-5">Sponsored By</p>
                <div class="flex gap-2 justify-evenly flex-wrap">
                    <div class="avatar">
                        <div class="w-20 h-20 rounded-full">
                            <img src="https://tp.ugm.ac.id/wp-content/uploads/sites/1404/2019/04/logo-white.png"
                                alt="">
                        </div>
                    </div>

                    <div class="avatar">
                        <div class="w-20 h-20 rounded-full">
                            <img src="https://ncsc.publiccharters.org/sites/default/files/2022-01/napcs-con-logo.png"
                                alt="">
                        </div>
                    </div>

                    <div class="avatar">
                        <div class="w-20 h-20">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQXfxLAZBlbVRN8VKAmCc3ZytBeK5rJwAk-qw&usqp=CAU"
                                alt="">
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="w-20 h-20 rounded-full">
                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/e/e4/University_of_Arizona_seal.svg/1200px-University_of_Arizona_seal.svg.png"
                                alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-conference::layouts.main>
