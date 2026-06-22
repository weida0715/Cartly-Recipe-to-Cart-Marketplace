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

  document.querySelectorAll('[data-category-edit-open]').forEach(btn => {
    btn.addEventListener('click', () => {
      const dialog = document.getElementById(btn.dataset.dialogTarget || '');
      if (!dialog) return;
      if (typeof dialog.showModal === 'function') {
        dialog.showModal();
      } else {
        dialog.setAttribute('open', '');
      }
    });
  });

  document.querySelectorAll('[data-category-edit-dialog]').forEach(dialog => {
    dialog.addEventListener('click', e => {
      if (e.target === dialog) {
        dialog.close?.();
        dialog.removeAttribute('open');
      }
    });
    dialog.querySelectorAll('[data-dialog-close]').forEach(btn => {
      btn.addEventListener('click', () => {
        dialog.close?.();
        dialog.removeAttribute('open');
      });
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
        invalid: field => Number.isNaN(Number(field.value)),
        message: 'Price must be a valid number.',
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
        invalid: field => !/^-?\d+$/.test(field.value.trim()),
        message: 'Stock quantity must be a valid integer.',
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
        invalid: field => Number.isNaN(Number(field.value)),
        message: 'Package quantity must be a valid number.',
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

  // Scale displayed recipe ingredients and keep cart-preview servings in sync.
  document.querySelectorAll('[data-recipe-servings]').forEach(control => {
    const input = control.querySelector('input[type=number]');
    const baseServings = Math.max(1, Number(control.dataset.baseServings) || 1);
    const quantities = [...document.querySelectorAll('[data-ingredient-quantity]')];
    const targets = [...document.querySelectorAll('[data-recipe-servings-target]')];

    const formatQuantity = value => Number(value.toFixed(2)).toString();
    const updateServings = event => {
      const rawValue = input?.value || '';
      const parsed = parseInt(rawValue, 10);

      if (event?.type === 'input' && (Number.isNaN(parsed) || parsed < 1)) {
        return;
      }

      const servings = Math.max(1, parsed || 1);
      if (input && (event?.type === 'change' || !event)) {
        input.value = String(servings);
      }
      quantities.forEach(quantity => {
        const baseQuantity = Number(quantity.dataset.baseQuantity) || 0;
        quantity.textContent = formatQuantity(baseQuantity * servings / baseServings);
      });
      targets.forEach(target => { target.value = String(servings); });
    };

    input?.addEventListener('input', updateServings);
    input?.addEventListener('change', updateServings);
    updateServings();
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

  // Mock delivery tracking: persist delivery leg transitions after short demo delays.
  document.querySelectorAll('[data-tracking-status]').forEach(card => {
    const labelsByStatus = {
      pending: 'Order placed',
      accepted: 'Merchant accepted',
      preparing: 'Merchant preparing',
      ready_to_deliver: 'Ready to deliver',
      out_for_delivery: 'Out for delivery',
      delivered: 'Delivered',
      completed: 'Order complete',
      cancelled: 'Merchant cancelled',
    };
    const statusSteps = {
      pending: 1,
      accepted: 2,
      preparing: 3,
      ready_to_deliver: 4,
      out_for_delivery: 5,
      delivered: 6,
      completed: 7,
      cancelled: 2,
    };
    const fill = card.querySelector('[data-tracking-fill]');
    const label = card.querySelector('[data-tracking-label]');
    const dots = [...card.querySelectorAll('[data-tracking-dot]')];
    const badge = card.parentElement.querySelector('[data-tracking-badge]');
    const form = card.querySelector('form[data-auto-advance]');
    const statusInput = form?.querySelector('[name="status"]');
    const receivedForm = card.querySelector('[data-received-form]');
    const trackingUrl = card.dataset.trackingUrl;
    const animationDuration = 450;
    const pollInterval = 4000;
    let pollTimer = null;
    let transitionChain = Promise.resolve();

    const progressForStep = step => Math.max(0, Math.min(1, (step - 1) / 6));

    const setProgress = progress => {
      if (fill) {
        fill.style.width = `calc((100% - (100% / 7)) * ${progress})`;
      }
    };

    const setDotsForStep = step => {
      dots.forEach((dot, i) => dot.classList.toggle('is-done', i < step));
    };

    const render = (status, explicitStep = null) => {
      const step = explicitStep ?? statusSteps[status] ?? 1;
      card.dataset.trackingStatus = status;
      card.dataset.trackingStep = String(step);
      if (label) label.textContent = labelsByStatus[status] || labelsByStatus.pending;
      setProgress(progressForStep(step));
      setDotsForStep(step);
      if (badge) badge.textContent = status.replaceAll('_', ' ');
      if (receivedForm) receivedForm.hidden = status !== 'delivered';
    };

    const animateLine = (fromStep, toStep) => new Promise(resolve => {
      const toProgress = progressForStep(toStep);
      if (!fill) {
        setProgress(toProgress);
        resolve();
        return;
      }

      const finish = () => {
        fill.removeEventListener('transitionend', onTransitionEnd);
        window.clearTimeout(fallback);
        resolve();
      };

      const onTransitionEnd = event => {
        if (event.target === fill && event.propertyName === 'width') {
          finish();
        }
      };

      const fallback = window.setTimeout(finish, animationDuration + 50);
      fill.addEventListener('transitionend', onTransitionEnd, { once: true });

      setProgress(progressForStep(fromStep));
      window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
          setProgress(toProgress);
        });
      });
    });

    const animateToStatus = payload => {
      const status = payload?.status || 'pending';
      const nextStep = payload?.step || statusSteps[status] || 1;
      const currentStep = parseInt(card.dataset.trackingStep || '1', 10);

      if (nextStep <= currentStep) {
        render(status, nextStep);
        return Promise.resolve();
      }

      setDotsForStep(currentStep);
      return animateLine(currentStep, nextStep).then(() => {
        render(status, nextStep);
      });
    };

    const queueStatusUpdate = payload => {
      transitionChain = transitionChain.then(() => animateToStatus(payload));
      return transitionChain;
    };

    const syncTrackingStatus = () => {
      if (!trackingUrl) return Promise.resolve();

      return fetch(trackingUrl, {
        headers: { 'X-Requested-With': 'fetch' },
      }).then(response => {
        if (!response.ok) throw new Error('Tracking refresh failed.');
        return response.json();
      }).then(payload => {
        const liveStatus = payload?.status || 'pending';
        const liveStep = payload?.step || statusSteps[liveStatus] || 1;
        const currentStatus = card.dataset.trackingStatus || 'pending';
        const currentStep = parseInt(card.dataset.trackingStep || '1', 10);

        if (liveStatus === currentStatus && liveStep === currentStep) {
          return;
        }

        return queueStatusUpdate(payload);
      }).catch(() => undefined);
    };

    const startPolling = () => {
      if (!trackingUrl || pollTimer) return;
      pollTimer = window.setInterval(() => {
        syncTrackingStatus();
      }, pollInterval);
    };

    render(card.dataset.trackingStatus || 'pending');
    startPolling();
    if (!form) return;

    const advance = () => {
      const nextStatus = statusInput?.value;
      if (!nextStatus) return;
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'fetch' },
      }).then(response => {
        if (!response.ok) throw new Error('Delivery update failed.');
        return response.json().catch(() => ({ status: nextStatus }));
      }).then(payload => {
        const appliedStatus = payload?.status || nextStatus;
        const appliedStep = payload?.step || statusSteps[appliedStatus] || 1;
        return queueStatusUpdate({ status: appliedStatus, step: appliedStep }).then(() => {
          if (appliedStatus === 'out_for_delivery') {
            statusInput.value = 'delivered';
            setTimeout(advance, parseInt(form.dataset.autoAdvance || '10000', 10));
          } else {
            form.remove();
          }
        });
      }).catch(() => window.location.reload());
    };

    setTimeout(advance, parseInt(form.dataset.autoAdvance || '10000', 10)); // ponytail: mock delivery timing only; replace with real carrier events later.
  });

  // D3 dashboard charts.
  if (window.d3) {
    const colors = ['#2e7d32', '#f57c00', '#1e40af', '#d97706', '#dc2626', '#6b7280'];

    const parseChartData = el => {
      try {
        return JSON.parse(el.dataset.chartValues || '[]').filter(item => Number.isFinite(Number(item.value)));
      } catch {
        return [];
      }
    };

    const addTooltip = el => {
      const tooltip = d3.select(el).append('div').attr('class', 'chart-tooltip').style('opacity', 0);
      return {
        show(event, text) {
          tooltip.text(text)
            .style('opacity', 1)
            .style('left', `${event.offsetX + 12}px`)
            .style('top', `${event.offsetY + 12}px`);
        },
        hide() {
          tooltip.style('opacity', 0);
        },
      };
    };

    const drawLegend = (wrap, data, color) => {
      const legend = wrap.append('div').attr('class', 'chart-legend');
      data.forEach((item, i) => {
        const row = legend.append('span');
        row.append('i').style('background', color(i));
        row.append('span').text(`${item.label}: ${item.value}`);
      });
    };

    const drawPie = (el, data) => {
      const size = 220;
      const radius = size / 2 - 8;
      const color = d3.scaleOrdinal(colors);
      const svg = d3.select(el).append('svg')
        .attr('viewBox', `0 0 ${size} ${size}`)
        .attr('role', 'img');
      const group = svg.append('g').attr('transform', `translate(${size / 2},${size / 2})`);
      group.selectAll('path')
        .data(d3.pie().value(d => d.value)(data))
        .join('path')
        .attr('d', d3.arc().innerRadius(52).outerRadius(radius))
        .attr('fill', (_, i) => color(i))
        .append('title')
        .text(d => `${d.data.label}: ${d.data.value}`);
      drawLegend(d3.select(el), data, color);
    };

    const drawBar = (el, data) => {
      const prefix = el.dataset.valuePrefix || '';
      const tooltip = addTooltip(el);
      const width = 520;
      const height = 260;
      const margin = { top: 18, right: 18, bottom: 54, left: 42 };
      const color = d3.scaleOrdinal(colors);
      const x = d3.scaleBand().domain(data.map(d => d.label)).range([margin.left, width - margin.right]).padding(0.24);
      const y = d3.scaleLinear().domain([0, d3.max(data, d => d.value) || 1]).nice().range([height - margin.bottom, margin.top]);
      const svg = d3.select(el).append('svg')
        .attr('viewBox', `0 0 ${width} ${height}`)
        .attr('role', 'img');

      svg.append('g')
        .attr('transform', `translate(0,${height - margin.bottom})`)
        .call(d3.axisBottom(x))
        .selectAll('text')
        .attr('transform', 'rotate(-25)')
        .style('text-anchor', 'end');

      svg.append('g')
        .attr('transform', `translate(${margin.left},0)`)
        .call(d3.axisLeft(y).ticks(5));

      svg.selectAll('rect')
        .data(data)
        .join('rect')
        .attr('x', d => x(d.label))
        .attr('y', d => y(d.value))
        .attr('width', x.bandwidth())
        .attr('height', d => y(0) - y(d.value))
        .attr('rx', 6)
        .attr('fill', (_, i) => color(i))
        .on('mousemove', (event, d) => tooltip.show(event, `${d.label}: ${prefix}${d.value}`))
        .on('mouseleave', tooltip.hide)
        .append('title')
        .text(d => `${d.label}: ${prefix}${d.value}`);
    };

    const drawLine = (el, data) => {
      const prefix = el.dataset.valuePrefix || '';
      const tooltip = addTooltip(el);
      const width = 520;
      const height = 260;
      const margin = { top: 18, right: 24, bottom: 42, left: 42 };
      const x = d3.scalePoint().domain(data.map(d => d.label)).range([margin.left, width - margin.right]).padding(0.5);
      const y = d3.scaleLinear().domain([0, d3.max(data, d => d.value) || 1]).nice().range([height - margin.bottom, margin.top]);
      const svg = d3.select(el).append('svg')
        .attr('viewBox', `0 0 ${width} ${height}`)
        .attr('role', 'img');

      svg.append('g')
        .attr('transform', `translate(0,${height - margin.bottom})`)
        .call(d3.axisBottom(x));

      svg.append('g')
        .attr('transform', `translate(${margin.left},0)`)
        .call(d3.axisLeft(y).ticks(5));

      svg.append('path')
        .datum(data)
        .attr('fill', 'none')
        .attr('stroke', colors[0])
        .attr('stroke-width', 3)
        .attr('d', d3.line().x(d => x(d.label)).y(d => y(d.value)));

      svg.selectAll('circle')
        .data(data)
        .join('circle')
        .attr('cx', d => x(d.label))
        .attr('cy', d => y(d.value))
        .attr('r', 5)
        .attr('fill', colors[1])
        .on('mousemove', (event, d) => tooltip.show(event, `${d.label}: ${prefix}${d.value}`))
        .on('mouseleave', tooltip.hide)
        .append('title')
        .text(d => `${d.label}: ${prefix}${d.value}`);
    };

    document.querySelectorAll('[data-chart]').forEach(el => {
      const data = parseChartData(el);
      if (!data.length || (el.dataset.chart === 'pie' && !data.some(item => Number(item.value) > 0))) {
        el.textContent = 'No chart data available.';
        return;
      }
      if (el.dataset.chart === 'pie') drawPie(el, data);
      if (el.dataset.chart === 'bar') drawBar(el, data);
      if (el.dataset.chart === 'line') drawLine(el, data);
    });
  }
});
