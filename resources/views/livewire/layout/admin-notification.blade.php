<x-dropdown>
                            <x-slot name="trigger">
                                <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fa fa-envelope"></i>
                                </button>
                            </x-slot>
                            <x-dropdown-link href="#">
                                <div class="notif-img">
                                    <img src="{{ url('/') }}/assets/img/jm_denis.jpg" alt="Img Profile" />
                                </div>
                                <div class="notif-content">
                                    <span class="subject">Jimmy Denis</span>
                                    <span class="block"> How are you ? </span>
                                    <span class="time">5 minutes ago</span>
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                <div class="notif-img">
                                    <img src="{{ url('/') }}/assets/img/chadengle.jpg" alt="Img Profile" />
                                </div>
                                <div class="notif-content">
                                    <span class="subject">Chad</span>
                                    <span class="block"> Ok, Thanks ! </span>
                                    <span class="time">12 minutes ago</span>
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                <div class="notif-img">
                                    <img src="{{ url('/') }}/assets/img/mlane.jpg" alt="Img Profile" />
                                </div>
                                <div class="notif-content">
                                    <span class="subject">Jhon Doe</span>
                                    <span class="block">
                                        Ready for the meeting today...
                                    </span>
                                    <span class="time">12 minutes ago</span>
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                <div class="notif-img">
                                    <img src="{{ url('/') }}/assets/img/talha.jpg" alt="Img Profile" />
                                </div>
                                <div class="notif-content">
                                    <span class="subject">Talha</span>
                                    <span class="block"> Hi, Apa Kabar ? </span>
                                    <span class="time">17 minutes ago</span>
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link href="javascript:void(0);">
                                See all messages<i class="fa fa-angle-right"></i>
                            </x-dropdown-link>
                        </x-dropdown>
