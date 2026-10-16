@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="aboutUs">
            <div class="col-md-12 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.contact_us') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.modules.contact_us') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    {{-- ========================================================== --}}
    {{-- ==================== Page Body Section =================== --}}
    {{-- ========================================================== --}}
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table id="file_export_main_info_part_1" class="table table-striped table-bordered display">
                                <thead>
                                    {{--  Email: --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.email') }}:
                                        </th>
                                        <td> <strong>
                                                {{ isset($contactUs->email) ? $contactUs->email : '----' }}
                                            </strong>
                                        </td>
                                    </tr>

                                    {{-- phone --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.phone') }}:</th>
                                        <td>
                                            <strong>{{ isset($contactUs->phone) ? $contactUs->phone : '----' }}
                                            </strong>
                                        </td>
                                    </tr>

                                    {{-- whatsapp --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.contact_us.whatsapp') }}:</th>
                                        <td>
                                            <strong>{{ isset($contactUs->whatsapp) ? $contactUs->whatsapp : '----' }}
                                            </strong>
                                        </td>
                                    </tr>

                                    {{-- facebook_url --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Facebook']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->facebook) ? $contactUs->facebook : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- youtube_url --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Youtube']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->youtube) ? $contactUs->youtube : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- tiktok --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Tiktok']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->tiktok) ? $contactUs->tiktok : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- telegram --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Telegram']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->telegram) ? $contactUs->telegram : '-------' !!} </strong>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table id="file_export_status_team" class="table table-striped table-bordered display">
                                <thead>
                                    {{-- address_ar --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.contact_us.address_ar') }} :</th>
                                        <td>
                                            <strong> {!! isset($contactUs->address_ar) ? $contactUs->address_ar : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- address_en --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.contact_us.address_en') }} :</th>
                                        <td>
                                            <strong> {!! isset($contactUs->address_en) ? $contactUs->address_en : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- hours --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.contact_us.hours') }} :</th>
                                        <td>
                                            <strong>
                                                @if (isset($contactUs) && filled($contactUs->hours))
                                                    {!! nl2br(e($contactUs->hours)) !!}
                                                @else
                                                    ----
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>

                                    {{-- instagram_url --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Instagram']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->instagram) ? $contactUs->instagram : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- twitter --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Twitter']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->twitter) ? $contactUs->twitter : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- linkedin --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Linkedin']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->linkedin) ? $contactUs->linkedin : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                    {{-- snapchat --}}
                                    <tr>
                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.platform_url', ['platform' => 'Snapchat']) }}:</th>
                                        <td>
                                            <strong> {!! isset($contactUs->snapchat) ? $contactUs->snapchat : '-------' !!} </strong>
                                        </td>
                                    </tr>

                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit --}}
            <div class="border-bottom title-part-padding">
                <div class="row justify-content-end">
                    <div class="col-auto">
                        <a href="{{ route('super_admin.contact_us-edit', isset($contactUs->id) ? $contactUs->id : -1) }}"
                            class="btn btn-dark">
                            <i data-feather="plus" class="fill-white feather-sm"></i> {{ __('dashboard.shared.edit') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('extra_js')
@endsection
