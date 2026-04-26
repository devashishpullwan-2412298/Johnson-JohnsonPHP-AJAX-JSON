document.addEventListener('DOMContentLoaded', () => {
  const csrfEndpoint = 'api/csrf.php';

  fetch(csrfEndpoint)
    .then((response) => response.json())
    .then((data) => {
      if (data && data.token) {
        document.querySelectorAll('input[name=csrf_token]').forEach((input) => {
          input.value = data.token;
        });
      }
    })
    .catch(() => {});

  document.querySelectorAll('form[novalidate]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        alert('Please fill the form correctly.');
      }
    });
  });

  if (!window.jQuery || !document.getElementById('medicine-filter-form')) {
    return;
  }

  const $ = window.jQuery;
  const $status = $('#ajax-status');
  const $jsonStatus = $('#json-status');
  const $results = $('#medicine-results');
  const $jsonPreview = $('#json-preview');
  const csrfToken = $('input[name="csrf_token"]').first().val();

  function setStatus($element, kind, message) {
    $element.removeClass('info success error').addClass(kind).text(message);
  }

  function renderMedicines(items) {
    if (!items.length) {
      $results.html('<p class="muted">No medicines matched the current AJAX filters.</p>');
      return;
    }

    const cards = items.map((medicine) => {
      const prescriptionBadge = medicine.prescription_needed
        ? '<span class="pill pill-warn">Prescription Required</span>'
        : '<span class="pill">OTC</span>';

      return `
        <article class="result-card">
          <div class="result-top">
            <h3>${medicine.name}</h3>
            ${prescriptionBadge}
          </div>
          <p>${medicine.description || 'No description available.'}</p>
          <p><strong>Category:</strong> ${medicine.category}</p>
          <p><strong>Price:</strong> Rs ${Number(medicine.price).toFixed(2)}</p>
          <p><strong>Stock:</strong> ${medicine.stock}</p>
        </article>
      `;
    });

    $results.html(cards.join(''));
  }

  function loadMedicines() {
    setStatus($status, 'info', 'jQuery registered the event and is fetching filtered medicines via AJAX...');

    $.ajax({
      url: 'api/ajax_medicines.php',
      method: 'GET',
      dataType: 'json',
      data: {
        search: $('#search-term').val(),
        category: $('#category-filter').val(),
        prescription_only: $('#prescription-only').is(':checked') ? 1 : 0,
      },
    })
      .done((response) => {
        renderMedicines(response.medicines || []);
        setStatus(
          $status,
          'success',
          `AJAX success: captured ${response.count || 0} medicine record(s) and displayed them on the page.`
        );
      })
      .fail(() => {
        setStatus($status, 'error', 'AJAX request failed while loading medicines.');
      });
  }

  function handleJsonResponse(response) {
    const errors = response.errors || [];
    const prefix =
      response.action === 'create'
        ? 'JSON creation finished'
        : 'JSON consumption finished';

    setStatus(
      $jsonStatus,
      response.ok ? 'success' : 'error',
      response.ok
        ? `${prefix} and the schema validation passed.`
        : `${prefix} but schema validation reported: ${errors.join(' | ')}`
    );

    $jsonPreview.text(JSON.stringify(response.payload || {}, null, 2));
  }

  $('#load-medicines').on('click', loadMedicines);
  $('#search-term').on('keyup', loadMedicines);
  $('#category-filter, #prescription-only').on('change', loadMedicines);

  $('#create-json').on('click', () => {
    setStatus($jsonStatus, 'info', 'Creating the JSON file from PHP and validating it against the JSON schema...');

    $.ajax({
      url: 'api/catalog_json.php',
      method: 'POST',
      dataType: 'json',
      data: {
        csrf_token: csrfToken,
      },
    })
      .done(handleJsonResponse)
      .fail(() => {
        setStatus($jsonStatus, 'error', 'Failed to create and validate the JSON file.');
      });
  });

  $('#consume-json').on('click', () => {
    setStatus($jsonStatus, 'info', 'Consuming the JSON file through AJAX and validating it at read time...');

    $.ajax({
      url: 'api/catalog_json.php',
      method: 'GET',
      dataType: 'json',
    })
      .done(handleJsonResponse)
      .fail(() => {
        setStatus($jsonStatus, 'error', 'Failed to consume the JSON file.');
      });
  });
});
