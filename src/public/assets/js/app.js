// Cartly base interactions
document.addEventListener('DOMContentLoaded', () => {
  // Auto-dismiss flash messages
  document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; }, 4000);
    setTimeout(() => el.remove(), 4500);
  });

  // Confirm-dialog forms: <form data-confirm="Are you sure?">
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', e => {
      if (!confirm(form.dataset.confirm)) e.preventDefault();
    });
  });

  // Merchant product validation message.
  document.querySelectorAll('form[data-product-form]').forEach(form => {
    const dialog = document.querySelector('[data-product-validation-dialog]');

    const validations = [
      {
        field: form.querySelector('[name="product_name"]'),
        invalid: field => field.value.trim() === '',
        message: 'Product name is required.',
      },
      {
        field: form.querySelector('[name="price"]'),
        invalid: field => field.value.trim() === '',
        message: 'Price is required.',
      },
      {
        field: form.querySelector('[name="price"]'),
        invalid: field => Number(field.value) < 0,
        message: 'Price cannot be negative.',
      },
      {
        field: form.querySelector('[name="stock_quantity"]'),
        invalid: field => field.value.trim() === '',
        message: 'Stock quantity is required.',
      },
      {
        field: form.querySelector('[name="stock_quantity"]'),
        invalid: field => Number(field.value) < 0,
        message: 'Stock quantity cannot be negative.',
      },
      {
        field: form.querySelector('[name="package_quantity"]'),
        invalid: field => field.value.trim() === '',
        message: 'Package quantity is required.',
      },
      {
        field: form.querySelector('[name="package_quantity"]'),
        invalid: field => Number(field.value) <= 0,
        message: 'Package quantity must be greater than zero.',
      },
    ];

    const showValidationDialog = text => {
      if (!dialog) {
        return;
      }
      dialog.textContent = text;
      dialog.hidden = false;
    };

    const clearValidationDialog = () => {
      if (dialog) {
        dialog.textContent = '';
        dialog.hidden = true;
      }
    };

    const validateProductForm = () => {
      const failed = validations.find(({ field, invalid }) => field && invalid(field));
      if (failed) {
        showValidationDialog(failed.message);
        return failed;
      }
      clearValidationDialog();
      return null;
    };

    validations.forEach(({ field }) => {
      field?.addEventListener('input', validateProductForm);
      field?.addEventListener('blur', validateProductForm);
    });

    form.addEventListener('submit', e => {
      const failed = validateProductForm();
      if (!failed) return;
      e.preventDefault();
      failed.field.focus({ preventScroll: true });
    });
  });

  // Quantity steppers: [data-stepper] containing [data-step="-1|1"] and an input
  document.querySelectorAll('[data-stepper]').forEach(box => {
    const input = box.querySelector('input[type=number]');
    box.querySelectorAll('[data-step]').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const step = parseInt(btn.dataset.step, 10) || 1;
        const min = parseInt(input.min || '1', 10);
        const next = Math.max(min, (parseInt(input.value, 10) || 0) + step);
        input.value = next;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
  });

  // Reset filtered listing results when a search input is cleared.
  document.querySelectorAll('form[data-search-reset]').forEach(form => {
    form.querySelectorAll('[data-search-reset-input]').forEach(input => {
      input.addEventListener('search', () => {
        if (input.value === '') {
          form.submit();
        }
      });
    });
  });
});
