<x-conference::layouts.main>
    <div class="px-6 py-2 mt-6">
        <p class="prose prose-lg text-heading">Current Conference</p>
    </div>
    <div class="card px-5 py-3 -mt-2">
        <div class="card-body space-y-2 border rounded">
            <div class="cf-current p-4 -mt-1">
                <p class="prose prose-lg text-heading -mt-2">{{ $currentConference->name }}</p>
                @if ($currentConference->hasMeta('date_held'))
                    <div class="inline-flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                        <time class="prose text-xs text-secondary">{{ $currentConference->getMeta('date_held') }}</time>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row space-x-4">
                    @if ($currentConference->hasMedia('thumbnail'))
                        <div class="cf-thumbnail sm:max-w-[10rem]">
                            <img class="h-full w-full rounded object-cover sm:object-left-top"
                                src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}" alt="" />
                        </div>
                    @endif
                    <div class="flex flex-col mt-3 gap-2">
                        @if ($currentConference->hasMeta('description'))
                            <div class="cf-description prose prose-sm text-secondary -mt-2">
                                <p>{!! $currentConference->getMeta('description') !!}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card ps-4">
        <div class="card-body space-y-2">
            <div class="cf-information">
                <p class="pb-3 prose prose-lg text-heading">Information</p>
                @if ($currentConference->hasMeta('date_held') || $currentConference->hasMeta('location'))
                    <table class="w-full" cellpadding="4">
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
                <p class="pb-2 prose prose-lg text-heading">Quota</p>
                <table class="w-full" cellpadding="4">
                    <tr>
                        <td width="80">Papers</td>
                        <td width="20">:</td>
                        <td width="90">
                            <span>400 Papers</span>
                        </td>
                        <td width="10">
                            <span class="w-30 badge h-5 w-24 px-2 py-2 text-mini">400 Accepted</span>
                        </td>
                        <td>
                            <span class="badge badge-primary badge-outline h-5 w-24 px-2 py-2 text-mini">40
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
                            <span class="badge h-5 w-24 px-2 py-2 text-mini">300 Accepted</span>
                        </td>
                        <td>
                            <span class="badge badge-primary badge-outline h-5 w-24 px-2 py-2 text-mini">30
                                Available</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="cf-contact">
                <h4 class="prose prose-lg text-heading pb-2">Contact</h4>
                <table class="w-full" cellpadding="4">
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
            </div>
        </div>
    </div>

    <div class="card ms-5 lg:w-[90%]">
        <div class="card-body space-y-2">
            <p class="prose prose-lg text-heading">Keynote Speakers</p>

            <div class="cf-speakers">
                <div class="flex flex-wrap gap-9">
                    <div class="flex flex-col space-y-4">
                        <p class="prose prose-sm text-secondary">Opening Speakers</p>
                        <div class="flex items-center space-x-2">
                            <div class="avatar">
                                <div
                                    class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                    <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                        alt="" />
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-xs text-secondary">Prof. Ashton Faisal, Ph.D</p>
                                <small class="text-mini text-primary">"Technology Quantum"</small>
                                <small class="text-mini text-secondary">University, Japan</small>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-4">
                        <p class="prose prose-sm text-secondary">Closing Speakers</p>
                        <div class="me-10 flex items-center space-x-2">
                            <div class="avatar">
                                <div
                                    class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                    <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                        alt="" />
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-xs text-secondary">Prof. Ashton Ganjar Ph.D</p>
                                <small class="text-mini text-primary">"Technology Quantum"</small>
                                <small class="text-mini text-secondary">University, Japan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <div class="max-w-[200px]">
                    <p class="prose prose-sm text-secondary">On Stage Speakers</p>
                </div>
                <div class="flex flex-wrap space-y-4 gap-4 md:justify-start">

                    <div class="me-10 flex items-center space-x-2">
                        <div class="avatar">
                            <div class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                <img src="https://www.mnp.ca/-/media/foundation/integrations/personnel/2020/12/16/13/57/personnel-image-4483.jpg?h=800&w=600&hash=9D5E5FCBEE00EB562DCD8AC8FDA8433D"
                                    alt="" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-xs">Prof. Ashton, Ph.D</p>
                            <small class="text-mini text-primary">"Technology Quantum"</small>
                            <small class="text-mini text-secondary">University, Japan</small>
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
                            <p class="text-xs">Prof. Ashton, Ph.D</p>
                            <small class="text-mini text-primary">"Technology Quantum"</small>
                            <small class="text-mini text-secondary">University, Japan</small>
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
                            <p class="text-xs">Prof. Ashton, Ph.D</p>
                            <small class="text-mini text-primary">"Technology Quantum"</small>
                            <small class="text-mini text-secondary">University, Japan</small>
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
                            <p class="text-xs">Prof. Ashton, Ph.D</p>
                            <small class="text-mini text-primary">"Technology Quantum"</small>
                            <small class="text-mini text-secondary">University, Japan</small>
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
                            <p class="text-xs">Prof. Ashton, Ph.D</p>
                            <small class="text-mini text-primary">"Technology Quantum"</small>
                            <small class="text-mini text-secondary">University, Japan</small>
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
                            <p class="text-xs">Prof. Ashton, Ph.D</p>
                            <small class="text-mini text-primary">"Technology Quantum"</small>
                            <small class="text-mini text-secondary">University, Japan</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="card mt-4 px-5">
        <div class="card-body">
            <div class="prose prose-lg text-heading">Registration Fee</div>
            <div class="grid grid-cols-1 items-center justify-center gap-6 p-4 text-center md:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 border bg-gray-50 rounded">
                <div class="grid grid-flow-row space-y-2 text-center">
                  <h2 class="font-semibold text-secondary">Author Cluster</h2>
                  <p>International Author</p>
                  <p>Domestic Author</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                  <h2 class="font-semibold text-secondary">Fee</h2>
                  <p>USD 125</p>
                  <p>IDR 1.750.000</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                  <h2 class="font-semibold text-secondary">Early Bid Registration</h2>
                  <p>USD 75</p>
                  <p>IDR.1000.000</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                  <h2 class="font-semibold text-secondary">Participan Cluster</h2>
                  <p>Internation Cluster</p>
                  <p>Domestic Participant</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                  <h2 class="font-semibold text-secondary">Fee</h2>
                  <p class="text-secondary">USD 30</p>
                  <p>IDR.300.000</p>
                </div>

                <div class="grid grid-flow-row space-y-2">
                  <h2 class="font-semibold text-secondary">Early Bid Registration</h2>
                  <p>USD 25</p>
                  <p>IDR.250.000</p>
                </div>
              </div>


            <div class="mt-4 flex justify-end">
                <small class="prose prose-sm text-secondary">Register first to make any payment</small>
            </div>

            <div class="w-full">
                <div
                    class="mt-7 flex flex-wrap border border-gray-300 gap-3  bg-gray-50 justify-center md:justify-center rounded">
                    <div class="flex h-32 w-32 items-center justify-center">
                        <div class="flex flex-col text-center">
                            <span class="prose prose-2xl text-secondary">50</span>
                            <span class=" text-secondary prose prose-lg">Topics</span>
                        </div>
                    </div>

                    <div class="flex h-32 w-32 items-center justify-center">
                        <div class="flex flex-col text-center">
                            <span class="prose prose-2xl text-secondary">30</span>
                            <span class=" text-secondary prose prose-lg">Papers</span>
                        </div>
                    </div>

                    <div class="flex h-32 w-32 items-center justify-center">
                        <div class="flex flex-col text-center">
                            <span class="prose prose-2xl text-secondary">250</span>
                            <span class=" text-secondary prose prose-lg">Verificators</span>
                        </div>
                    </div>

                    <div class="flex h-32 w-32 items-center justify-center">
                        <div class="flex flex-col text-center">
                            <span class="text-secondary prose prose-2xl">30</span>
                            <span class=" text-secondary prose prose-lg">Country</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex flex-col gap-4">
                <p class="mb-5 text-center prose prose-lg  text-heading">Sponsored By</p>
                <div class="flex flex-wrap justify-center md:justify-evenly gap-2">
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
