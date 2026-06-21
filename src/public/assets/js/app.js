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
