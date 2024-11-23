<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
        <!-- Search Icon Dropdown -->
        <x-dropdown id="searchDropdown" icon="fa fa-search">
            <x-slot name="trigger">
                <i class="fa fa-search"></i>
            </x-slot>
            <x-slot name="content">
                <form class="navbar-left navbar-form nav-search">
                    <div class="input-group">
                        <input type="text" placeholder="Search ..." class="form-control" />
                    </div>
                </form>
            </x-slot>
        </x-dropdown>

        <!-- Message Icon Dropdown -->
        <x-dropdown id="messageDropdown" icon="fa fa-envelope" notification="4">
            <x-slot name="trigger">
                <i class="fa fa-envelope"></i>
                <span class="notification">4</span>
            </x-slot>
            <x-slot name="content">
                <div class="dropdown-title d-flex justify-content-between align-items-center">
                    Messages
                    <a href="#" class="small">Mark all as read</a>
                </div>
                <div class="message-notif-scroll scrollbar-outer">
                    <div class="notif-center">
                        <x-dropdown-link href="#" icon="jm_denis.jpg" text="Jimmy Denis" subtitle="How are you?" time="5 minutes ago" />
                        <x-dropdown-link href="#" icon="chadengle.jpg" text="Chad" subtitle="Ok, Thanks!" time="12 minutes ago" />
                        <x-dropdown-link href="#" icon="mlane.jpg" text="Jhon Doe" subtitle="Ready for the meeting today..." time="12 minutes ago" />
                        <x-dropdown-link href="#" icon="talha.jpg" text="Talha" subtitle="Hi, Apa Kabar?" time="17 minutes ago" />
                    </div>
                </div>
                <a class="see-all" href="#">See all messages<i class="fa fa-angle-right"></i></a>
            </x-slot>
        </x-dropdown>

        <!-- Notification Icon Dropdown -->
        <x-dropdown id="notificationDropdown" icon="fa fa-bell" notification="4">
            <x-slot name="trigger">
                <i class="fa fa-bell"></i>
                <span class="notification">4</span>
            </x-slot>
            <x-slot name="content">
                <div class="dropdown-title">You have 4 new notifications</div>
                <div class="notif-scroll scrollbar-outer">
                    <div class="notif-center">
                        <x-dropdown-link href="#" icon="fa-user-plus" text="New user registered" time="5 minutes ago" />
                        <x-dropdown-link href="#" icon="fa-comment" text="Rahmad commented on Admin" time="12 minutes ago" />
                        <x-dropdown-link href="#" icon="profile2.jpg" text="Reza sent messages to you" time="12 minutes ago" />
                        <x-dropdown-link href="#" icon="fa-heart" text="Farrah liked Admin" time="17 minutes ago" />
                    </div>
                </div>
                <a class="see-all" href="#">See all notifications<i class="fa fa-angle-right"></i></a>
            </x-slot>
        </x-dropdown>

        <!-- Quick Actions Dropdown -->
        <x-dropdown id="quickActionsDropdown" icon="fas fa-layer-group">
            <x-slot name="trigger">
                <i class="fas fa-layer-group"></i>
            </x-slot>
            <x-slot name="content">
                <div class="quick-actions-header">
                    <span class="title mb-1">Quick Actions</span>
                    <span class="subtitle op-7">Shortcuts</span>
                </div>
                <div class="quick-actions-scroll scrollbar-outer">
                    <div class="quick-actions-items">
                        <div class="row m-0">
                            <!-- Individual Action Links -->
                            <a class="col-6 col-md-4 p-0" href="#">
                                <div class="quick-actions-item">
                                    <div class="avatar-item bg-danger rounded-circle">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <span class="text">Calendar</span>
                                </div>
                            </a>
                            <!-- Add more action links as needed -->
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-dropdown>

        <!-- User Profile Dropdown -->
        <x-dropdown id="userDropdown">
            <x-slot name="trigger">
                <div class="avatar-sm">
                    <img src="{{ url('/') }}/assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
                </div>
                <span class="profile-username">
                    <span class="op-7">Hi,</span>
                    <span class="fw-bold">Hizrian</span>
                </span>
            </x-slot>
            <x-slot name="content">
                <div class="dropdown-user-scroll scrollbar-outer">
                    <li>
                        <div class="user-box">
                            <div class="avatar-lg">
                                <img src="{{ url('/') }}/assets/img/profile.jpg" alt="image profile" class="avatar-img rounded" />
                            </div>
                            <div class="u-text">
                                <h4>Hizrian</h4>
                                <p class="text-muted">hello@example.com</p>
                                <a href="profile.html" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">My Balance</a>
                        <a class="dropdown-item" href="#">Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Logout</a>
                    </li>
                </div>
            </x-slot>
        </x-dropdown>
    </ul>


</div>
