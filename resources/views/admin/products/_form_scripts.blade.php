<script>
    (function () {
        let specIndex = document.querySelectorAll('.spec-row').length;
        document.getElementById('add-spec-row')?.addEventListener('click', function () {
            const wrapper = document.getElementById('specs-wrapper');
            const row = document.createElement('div');
            row.className = 'row spec-row mb-2';
            row.innerHTML = `
                <div class="col-md-5"><input type="text" name="specs[${specIndex}][label]" class="form-control"></div>
                <div class="col-md-5"><input type="text" name="specs[${specIndex}][value]" class="form-control"></div>
                <div class="col-md-2"><input type="number" min="0" name="specs[${specIndex}][sort_order]" class="form-control" value="${specIndex}"></div>`;
            wrapper.appendChild(row);
            specIndex++;
        });

        let accessoryIndex = document.querySelectorAll('.accessory-row').length;
        document.getElementById('add-accessory-row')?.addEventListener('click', function () {
            const wrapper = document.getElementById('accessories-wrapper');
            const row = document.createElement('div');
            row.className = 'row accessory-row mb-2';
            row.innerHTML = `
                <div class="col-md-6"><input type="text" name="accessories[${accessoryIndex}][name]" class="form-control"></div>
                <div class="col-md-4"><input type="text" name="accessories[${accessoryIndex}][qty]" class="form-control"></div>
                <div class="col-md-2"><input type="number" min="0" name="accessories[${accessoryIndex}][sort_order]" class="form-control" value="${accessoryIndex}"></div>`;
            wrapper.appendChild(row);
            accessoryIndex++;
        });

        let optionIndex = document.querySelectorAll('.option-group').length;
        document.getElementById('add-option-group')?.addEventListener('click', function () {
            const wrapper = document.getElementById('options-wrapper');
            const card = document.createElement('div');
            card.className = 'card mb-3 option-group';
            card.innerHTML = `
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-8"><input type="text" name="options[${optionIndex}][name]" class="form-control"></div>
                        <div class="col-md-4"><input type="number" min="0" name="options[${optionIndex}][sort_order]" class="form-control" value="${optionIndex}"></div>
                    </div>
                    <div class="option-values"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm add-option-value">{{ __('dashboard.products.add_option_value') }}</button>
                </div>`;
            wrapper.appendChild(card);
            optionIndex++;
        });

        document.getElementById('options-wrapper')?.addEventListener('click', function (event) {
            const button = event.target.closest('.add-option-value');
            if (!button) return;
            const group = button.closest('.option-group');
            const oi = Array.from(document.querySelectorAll('.option-group')).indexOf(group);
            const valuesWrap = group.querySelector('.option-values');
            const vi = valuesWrap.querySelectorAll('.option-value-row').length;
            const row = document.createElement('div');
            row.className = 'row mb-2 option-value-row';
            row.innerHTML = `
                <div class="col-md-5"><input type="text" name="options[${oi}][values][${vi}][label]" class="form-control"></div>
                <div class="col-md-4"><input type="number" step="0.01" min="0" name="options[${oi}][values][${vi}][additional_price]" class="form-control" value="0.00"></div>
                <div class="col-md-3"><input type="number" min="0" name="options[${oi}][values][${vi}][sort_order]" class="form-control" value="${vi}"></div>`;
            valuesWrap.appendChild(row);
        });
    })();
</script>
