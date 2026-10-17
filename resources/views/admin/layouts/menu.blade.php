<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                {{-- User Profile --}}
                <li class="nav-small-cap">
                    <i class="mdi mdi-dots-horizontal"></i>
                    <span class="hide-menu">{{ __('dashboard.shared.modules.personal') }}</span>
                </li>

                {{-- Home --}}
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('super_admin.dashboard') }}">
                        <i class="mdi mdi-layers"></i>
                        <span>{{ __('dashboard.shared.modules.home') }}</span>
                    </a>
                </li>

                {{-- pages --}}
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                        aria-expanded="false">
                        <i class="mdi mdi-book-open-page-variant"></i>
                        <span class="hide-menu">{{ __('dashboard.shared.modules.pages') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">

                        {{-- about us --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.about_us-index') }}">
                                <i class="mdi mdi-information-variant"></i>
                                <span>{{ __('dashboard.shared.modules.about_us') }}</span>
                            </a>
                        </li>

                        {{-- contact us --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.contact_us-index') }}">
                                <i class="mdi mdi-contacts"></i>
                                <span>{{ __('dashboard.shared.modules.contact_us') }}</span>
                            </a>
                        </li>

                        {{-- privacy_policies --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.privacy_policies-index') }}">
                                <i class="mdi mdi-bullseye"></i>
                                <span>{{ __('dashboard.shared.modules.privacy_policy') }}</span>
                            </a>
                        </li>

                        {{-- terms_and_conditions --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.terms_and_conditions-index') }}">
                                <i class="mdi mdi-eye-outline"></i>
                                <span>{{ __('dashboard.shared.modules.terms_and_conditions') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Users --}}
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                        aria-expanded="false">
                        <i class="mdi mdi-account-circle"></i>
                        <span class="hide-menu">{{ __('dashboard.shared.modules.users') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">

                        {{-- users --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.users-index') }}">
                                <i class="mdi mdi-account"></i>
                                <span>{{ __('dashboard.shared.modules.users') }}</span>
                            </a>
                        </li>

                        {{-- admins --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.admins-index') }}">
                                <i class="mdi mdi-account-key"></i>
                                <span>{{ __('dashboard.shared.modules.admins') }}</span>
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- Website Layouts --}}
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                        aria-expanded="false">
                        <i class="mdi mdi-book-open-page-variant"></i>
                        <span class="hide-menu">{{ __('dashboard.shared.modules.website_layouts') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        {{-- slider --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.sliders-index') }}">
                                <i class="mdi mdi-file-image"></i>
                                <span>{{ __('dashboard.shared.modules.sliders') }}</span>
                            </a>
                        </li>

                        {{-- categories --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.categories-index') }}">
                                <i class="mdi mdi-tag-multiple"></i>
                                <span>{{ __('dashboard.shared.modules.categories') }}</span>
                            </a>
                        </li>

                        {{-- products --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.products-index') }}">
                                <i class="mdi mdi-package-variant-closed"></i>
                                <span>{{ __('dashboard.shared.modules.products') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Requests --}}
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                        aria-expanded="false">
                        <i class="mdi mdi-cellphone-link"></i>
                        <span class="hide-menu">{{ __('dashboard.shared.modules.requests') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        {{-- contact us requests --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark"
                                href="{{ route('super_admin.contact_us_requests-index') }}">
                                <i class="mdi mdi-contact-mail"></i>
                                <span>{{ __('dashboard.shared.modules.contact_us_requests') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- WEBSite --}}
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('frontend.home') }}" target="_blank">
                        <i class="mdi mdi-web"></i>
                        <span>{{ __('dashboard.shared.modules.website') }}</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
