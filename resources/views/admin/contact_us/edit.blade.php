@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.update_info') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.contact_us-index') }}">{{ __('dashboard.shared.modules.contact_us') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.update_info') }}</li>
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
        <div class="row">
            <div class="col-12">
                {{-- Form Section --}}
                <div class="card">
                    <div class="card-body">
                        <form
                            action="{{ route('super_admin.contact_us-update', isset($contactUs->id) ? $contactUs->id : -1) }}"
                            method="POST" enctype="multipart/form-data" id="contactUsForm">
                            @csrf
                            <div class="form">
                                <div class="row">
                                    {{-- page_title_en --}}
                                    <div class="col-md-12 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="contact-page-title-en">
                                            {{ __('dashboard.contact_us.page_title_en') }} :
                                            <strong class="text-danger">
                                                @error('page_title_en')
                                                    - {{ $message }}
                                                @enderror
                                            </strong>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" id="contact-page-title-en" name="page_title_en" maxlength="255"
                                                value="{{ old('page_title_en', isset($contactUs->page_title_en) ? $contactUs->page_title_en : null) }}"
                                                class="form-control @error('page_title_en') border-danger @enderror"
                                                placeholder="{{ __('dashboard.contact_us.page_title_en') }}">
                                        </div>
                                    </div>

                                    {{-- page_intro_en --}}
                                    <div class="col-md-12 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="contact-page-intro-en">
                                            {{ __('dashboard.contact_us.page_intro_en') }} :
                                            <strong class="text-danger">
                                                @error('page_intro_en')
                                                    - {{ $message }}
                                                @enderror
                                            </strong>
                                        </label>
                                        <div class="input-group">
                                            <textarea id="contact-page-intro-en" style="width: 90% !important" maxlength="5000"
                                                class="form-control @error('page_intro_en') border-danger @enderror" name="page_intro_en"
                                                placeholder="{{ __('dashboard.contact_us.page_intro_en') }}" rows="4">{{ old('page_intro_en', isset($contactUs->page_intro_en) ? $contactUs->page_intro_en : null) }}</textarea>
                                        </div>
                                    </div>

                                    {{--  email --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.email') }}:
                                            <strong class="text-danger">*</strong>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text mdi mdi-format-title"
                                                    id="inputGroupPrepend2"></span>
                                            </div>
                                            <input type="text" name="email" data-validation="required" required
                                                value="{{ old('email', isset($contactUs->email) ? $contactUs->email : null) }}"
                                                class="form-control @error('email') border-danger @enderror"
                                                id="validationServer01" placeholder="{{ __('dashboard.shared.email') }}">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- phone --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.phone') }}:
                                            <strong class="text-danger">
                                                *
                                                @error('phone')
                                                    -
                                                    {{ $message }}
                                                @enderror
                                            </strong>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text mdi mdi-format-title"
                                                    id="inputGroupPrepend2"></span>
                                            </div>
                                            <input type="text" name="phone" data-validation="required" required
                                                value="{{ old('phone', isset($contactUs->phone) ? $contactUs->phone : null) }}"
                                                class="form-control @error('phone') border-danger @enderror"
                                                id="validationServer01" placeholder="{{ __('dashboard.shared.phone') }}">
                                        </div>
                                    </div>

                                    {{-- whatsapp --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.contact_us.whatsapp') }}:
                                            <strong class="text-danger">

                                                @error('whatsapp')
                                                    -
                                                    {{ $message }}
                                                @enderror
                                            </strong>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text mdi mdi-format-title"
                                                    id="inputGroupPrepend2"></span>
                                            </div>
                                            <input type="text" name="whatsapp" data-validation="required" required
                                                value="{{ old('whatsapp', isset($contactUs->whatsapp) ? $contactUs->whatsapp : null) }}"
                                                class="form-control @error('whatsapp') border-danger @enderror"
                                                id="validationServer01" placeholder="{{ __('dashboard.contact_us.whatsapp') }}">
                                        </div>
                                    </div>

                                    {{-- facebook --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Facebook']) }} :
                                            <strong class="text-danger">  @error('facebook')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="facebook"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Facebook']) }}" rows="1">{{ old('facebook', isset($contactUs->facebook) ? $contactUs->facebook : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- instagram --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Instagram']) }} :
                                            <strong class="text-danger">  @error('instagram')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="instagram"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Instagram']) }}" rows="1">{{ old('instagram', isset($contactUs->instagram) ? $contactUs->instagram : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- youtube --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Youtube']) }} :
                                            <strong class="text-danger">  @error('youtube')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="youtube"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Youtube']) }}" rows="1">{{ old('youtube', isset($contactUs->youtube) ? $contactUs->youtube : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- linkedin --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Linkedin']) }} :
                                            <strong class="text-danger">  @error('linkedin')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="linkedin"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Linkedin']) }}" rows="1">{{ old('linkedin', isset($contactUs->linkedin) ? $contactUs->linkedin : null) }}</textarea>
                                        </div>
                                    </div>

                                    {{-- snapchat --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Snapchat']) }} :
                                            <strong class="text-danger">  @error('snapchat')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="snapchat"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Snapchat']) }}" rows="1">{{ old('snapchat', isset($contactUs->snapchat) ? $contactUs->snapchat : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- twitter --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Twitter']) }} :
                                            <strong class="text-danger">  @error('twitter')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="twitter"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Twitter']) }}" rows="1">{{ old('twitter', isset($contactUs->twitter) ? $contactUs->twitter : null) }}</textarea>
                                        </div>
                                    </div>

                                    {{-- snapchat --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Snapchat']) }} :
                                            <strong class="text-danger">  @error('snapchat')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="snapchat"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Snapchat']) }}" rows="1">{{ old('snapchat', isset($contactUs->snapchat) ? $contactUs->snapchat : null) }}</textarea>
                                        </div>
                                    </div>

                                    {{-- tiktok --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Tiktok']) }} :
                                            <strong class="text-danger"> @error('tiktok')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="tiktok"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Tiktok']) }}" rows="1">{{ old('tiktok', isset($contactUs->tiktok) ? $contactUs->tiktok : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- telegram --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.shared.platform_url', ['platform' => 'Telegram']) }} :
                                            <strong class="text-danger"> @error('telegram')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="telegram"
                                                placeholder="{{ __('dashboard.shared.platform_url', ['platform' => 'Telegram']) }}" rows="1">{{ old('telegram', isset($contactUs->telegram) ? $contactUs->telegram : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- address_ar --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.contact_us.address_ar') }} : <strong class="text-danger">
                                                @error('address_ar')
                                                    - {{ $message }}
                                                @enderror
                                            </strong>
                                        </label>
                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control ar" required name="address_ar"
                                                placeholder="{{ __('dashboard.contact_us.address_ar') }}" rows="8">{{ old('address_ar', isset($contactUs->address_ar) ? $contactUs->address_ar : null) }}</textarea>
                                        </div>
                                    </div>

                                    {{-- address_en --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="validationServer01">
                                            {{ __('dashboard.contact_us.address_en') }} :
                                            <strong class="text-danger"> @error('address_en')
                                                    - {{ $message }}
                                                @enderror </strong>
                                        </label>

                                        <div class="input-group">
                                            <textarea style="width: 90% !important" maxlength="1600" class="form-control"required name="address_en"
                                                placeholder="{{ __('dashboard.contact_us.address_en') }}" rows="8">{{ old('address_en', isset($contactUs->address_en) ? $contactUs->address_en : null) }}</textarea>

                                        </div>
                                    </div>

                                    {{-- hours --}}
                                    <div class="col-md-12 mb-3">
                                        <label class="text-dark font-weight-medium mb-3" for="contact-hours">
                                            {{ __('dashboard.contact_us.hours') }} :
                                            <strong class="text-danger">
                                                @error('hours')
                                                    - {{ $message }}
                                                @enderror
                                            </strong>
                                        </label>
                                        <div class="input-group">
                                            <textarea id="contact-hours" style="width: 90% !important" maxlength="5000"
                                                class="form-control @error('hours') border-danger @enderror" name="hours"
                                                placeholder="{{ __('dashboard.contact_us.hours') }}" rows="4">{{ old('hours', isset($contactUs->hours) ? $contactUs->hours : null) }}</textarea>
                                        </div>
                                    </div>


                                </div>

                                {{-- Button --}}
                                <div class="col-md-12">
                                    <button class="btn btn-primary" type="submit">{{ __('dashboard.shared.save_updates') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
@endsection
