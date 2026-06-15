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
      let hadSearchValue = input.value.trim() !== '';

      input.addEventListener('input', () => {
        const hasSearchValue = input.value.trim() !== '';
        if (hadSearchValue && !hasSearchValue) {
          form.submit();
        }
        hadSearchValue = hasSearchValue;
      });
    });
  });
});
