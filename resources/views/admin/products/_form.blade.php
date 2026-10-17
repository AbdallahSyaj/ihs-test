@php
    $isEdit = isset($product);
    $specs = old('specs', $isEdit ? $product->specs->map(fn ($s) => ['label' => $s->label, 'value' => $s->value, 'sort_order' => $s->sort_order])->values()->all() : [['label' => '', 'value' => '', 'sort_order' => 0]]);
    $accessories = old('accessories', $isEdit ? $product->accessories->map(fn ($a) => ['name' => $a->name, 'qty' => $a->qty, 'sort_order' => $a->sort_order])->values()->all() : [['name' => '', 'qty' => '', 'sort_order' => 0]]);
    $options = old('options', $isEdit ? $product->options->map(fn ($o) => [
        'name' => $o->name,
        'sort_order' => $o->sort_order,
        'values' => $o->values->map(fn ($v) => ['label' => $v->label, 'additional_price' => $v->additional_price, 'sort_order' => $v->sort_order])->values()->all(),
    ])->values()->all() : []);
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.products.category') }}</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">—</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $isEdit ? $product->category_id : null) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.shared.status') }}</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="active" @selected(old('status', $isEdit ? $product->status : 'active') === 'active')>{{ __('dashboard.shared.active') }}</option>
            <option value="inactive" @selected(old('status', $isEdit ? $product->status : '') === 'inactive')>{{ __('dashboard.shared.inactive') }}</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.shared.name') }}</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $isEdit ? $product->name : '') }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.products.slug') }}</label>
        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $isEdit ? $product->slug : '') }}" autocomplete="off">
        <small class="text-muted">{{ __('dashboard.products.slug_help') }}</small>
        @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.products.model') }}</label>
        <input type="text" name="model" class="form-control" value="{{ old('model', $isEdit ? $product->model : '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.products.base_price') }}</label>
        <input type="number" step="0.01" min="0" name="base_price" class="form-control @error('base_price') is-invalid @enderror" value="{{ old('base_price', $isEdit ? $product->base_price : '') }}">
        @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('dashboard.products.badge') }}</label>
        <input type="text" name="badge" class="form-control" value="{{ old('badge', $isEdit ? $product->badge : '') }}">
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">{{ __('dashboard.products.short_description') }}</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">{{ __('dashboard.products.long_description') }}</label>
        <textarea name="long_description" class="form-control" rows="5">{{ old('long_description', $isEdit ? $product->long_description : '') }}</textarea>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">{{ __('dashboard.products.compatibility') }}</label>
        <textarea name="compatibility" class="form-control" rows="3">{{ old('compatibility', $isEdit ? $product->compatibility : '') }}</textarea>
    </div>
</div>

<hr>
<h5>{{ __('dashboard.products.listing_image') }}</h5>
<div class="mb-3">
    <input type="file" name="listing_image" class="form-control @error('listing_image') is-invalid @enderror" accept="image/png,image/jpeg,image/gif">
    @error('listing_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@if ($isEdit && $product->images->isNotEmpty())
    <p class="fw-bold">{{ __('dashboard.products.main_listing_image') }}</p>
    <div class="row mb-3">
        @foreach ($product->images as $image)
            <div class="col-md-4 mb-2 border rounded p-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="listing_image_id" value="{{ $image->id }}" id="listing-{{ $image->id }}"
                        @checked((int) old('listing_image_id', $product->images->firstWhere('is_listing', true)?->id) === $image->id)>
                    <label class="form-check-label" for="listing-{{ $image->id }}">{{ __('dashboard.products.main_listing_image') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" id="remove-{{ $image->id }}">
                    <label class="form-check-label" for="remove-{{ $image->id }}">{{ __('dashboard.products.remove_image') }}</label>
                </div>
                @if (! str_starts_with($image->path, 'http'))
                    <img src="{{ asset($image->path) }}" alt="" class="img-fluid mt-2" style="max-height:120px">
                @else
                    <p class="small text-muted mt-2">{{ $image->path }}</p>
                @endif
                @if (! $image->is_listing)
                    <label class="form-label small mt-2">{{ __('dashboard.products.gallery_order') }}</label>
                    <input type="number" min="0" name="existing_gallery_orders[{{ $image->id }}]" class="form-control form-control-sm"
                        value="{{ old('existing_gallery_orders.'.$image->id, $image->gallery_order) }}">
                @endif
            </div>
        @endforeach
    </div>
@endif

<h5>{{ __('dashboard.products.gallery_images') }}</h5>
<div class="mb-3">
    <input type="file" name="gallery_images[]" class="form-control @error('gallery_images.*') is-invalid @enderror" accept="image/png,image/jpeg,image/gif" multiple>
    @error('gallery_images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

<hr>
<h5>{{ __('dashboard.products.specifications') }}</h5>
<div id="specs-wrapper">
    @foreach ($specs as $i => $spec)
        <div class="row spec-row mb-2">
            <div class="col-md-5"><input type="text" name="specs[{{ $i }}][label]" class="form-control" placeholder="{{ __('dashboard.products.spec_label') }}" value="{{ $spec['label'] ?? '' }}"></div>
            <div class="col-md-5"><input type="text" name="specs[{{ $i }}][value]" class="form-control" placeholder="{{ __('dashboard.products.spec_value') }}" value="{{ $spec['value'] ?? '' }}"></div>
            <div class="col-md-2"><input type="number" min="0" name="specs[{{ $i }}][sort_order]" class="form-control" value="{{ $spec['sort_order'] ?? $i }}"></div>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-spec-row">{{ __('dashboard.products.add_row') }}</button>

<h5>{{ __('dashboard.products.accessories') }}</h5>
<div id="accessories-wrapper">
    @foreach ($accessories as $i => $row)
        <div class="row accessory-row mb-2">
            <div class="col-md-6"><input type="text" name="accessories[{{ $i }}][name]" class="form-control" placeholder="{{ __('dashboard.shared.name') }}" value="{{ $row['name'] ?? '' }}"></div>
            <div class="col-md-4"><input type="text" name="accessories[{{ $i }}][qty]" class="form-control" placeholder="{{ __('dashboard.products.accessory_qty') }}" value="{{ $row['qty'] ?? '' }}"></div>
            <div class="col-md-2"><input type="number" min="0" name="accessories[{{ $i }}][sort_order]" class="form-control" value="{{ $row['sort_order'] ?? $i }}"></div>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-accessory-row">{{ __('dashboard.products.add_row') }}</button>

<h5>{{ __('dashboard.products.options') }}</h5>
<div id="options-wrapper">
    @foreach ($options as $oi => $option)
        <div class="card mb-3 option-group" data-option-index="{{ $oi }}">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-8">
                        <input type="text" name="options[{{ $oi }}][name]" class="form-control" placeholder="{{ __('dashboard.products.option_name') }}" value="{{ $option['name'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <input type="number" min="0" name="options[{{ $oi }}][sort_order]" class="form-control" value="{{ $option['sort_order'] ?? $oi }}">
                    </div>
                </div>
                <div class="option-values">
                    @foreach ($option['values'] ?? [] as $vi => $value)
                        <div class="row mb-2 option-value-row">
                            <div class="col-md-5"><input type="text" name="options[{{ $oi }}][values][{{ $vi }}][label]" class="form-control" value="{{ $value['label'] ?? '' }}" placeholder="{{ __('dashboard.products.option_value') }}"></div>
                            <div class="col-md-4"><input type="number" step="0.01" min="0" name="options[{{ $oi }}][values][{{ $vi }}][additional_price]" class="form-control" value="{{ $value['additional_price'] ?? '0.00' }}"></div>
                            <div class="col-md-3"><input type="number" min="0" name="options[{{ $oi }}][values][{{ $vi }}][sort_order]" class="form-control" value="{{ $value['sort_order'] ?? $vi }}"></div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm add-option-value">{{ __('dashboard.products.add_option_value') }}</button>
            </div>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-outline-secondary btn-sm" id="add-option-group">{{ __('dashboard.products.add_option') }}</button>
