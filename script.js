document.addEventListener('DOMContentLoaded', () => {
  //loadOrders();
  loadCountries();
  loadStatus();

  //actualizaciones al crear y actualizar pedidos
  document.getElementById('edit-form').addEventListener('submit', updateOrder);
  document.getElementById('new-form').addEventListener('submit', createOrder);

  //filtros
  document.getElementById('filter-country').addEventListener('change', loadOrders);
  document.getElementById('filter-status').addEventListener('change', loadOrders);
});

function loadOrders() {
  const country = document.getElementById('filter-country').value;
  const status = document.getElementById('filter-status').value;

  fetch(`fetch.php?country=${country}&status=${status}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#orders-table tbody');
      tbody.innerHTML = '';

      data.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
        <td>${order.OrderId}</td>
        <td>${order.DateOrder}</td>
        <td>${order.CustomerFullname}</td>
        <td>${order.CustomerAdress}</td>
        <td>${order.OrderCountry}</td>
        <td>${order.ProductsOrdered}</td>
        <td>${order.OrderStatus}</td>
        `;
        row.addEventListener('click', () => openPopup(order));
        tbody.appendChild(row);
      });
    });
}

function loadCountries() {
  fetch('fetch.php')
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('filter-country');
      const countries = [...new Set(data.map(order => order.OrderCountry))];
      select.innerHTML = '<option value="">Todos</option>';
      countries.forEach(pais => {
        const option = document.createElement('option');
        option.value = pais;
        option.textContent = pais;
        select.appendChild(option);
      });
    });
}

/*function loadStatus() {
    fetch('get_status.php')
      .then(res => res.json())
      .then(data => {
        const select = document.getElementById('filter-status');
        select.innerHTML = '<option value="">Todos</option>';
        data.forEach(status => {
          const option = document.createElement('option');
          option.value = status;
          option.textContent = status;
          select.appendChild(option);
        });
      });
  }*/

function loadStatus() {
    fetch('get_status.php')
        .then(res => {
            if (!res.ok) throw new Error('Error al obtener estados');
            return res.json();
        })
        .then(data => {
            const filterSelect = document.getElementById('filter-status');
            const editSelect = document.getElementById('edit-status');
            const newSelect = document.querySelector('select[name="status"]');
        
            filterSelect.innerHTML = '<option value="">Todos</option>';
            editSelect.innerHTML = '';
            newSelect.innerHTML = '';
        
            data.forEach(status => {
                const filterOption = document.createElement('option');
                filterOption.value = status;
                filterOption.textContent = status;
                filterSelect.appendChild(filterOption);
        
                const editOption = document.createElement('option');
                editOption.value = status;
                editOption.textContent = status;
                editSelect.appendChild(editOption);
        
                const newOption = document.createElement('option');
                newOption.value = status;
                newOption.textContent = status;
                newSelect.appendChild(newOption);
            });
        
            filterSelect.value = "Aceptados y en proceso";
            loadOrders();

            if (newSelect.options.length > 0) {
                newSelect.selectedIndex = 0;
            }
        })
        .catch(error => {
            console.error('Error cargando estados:', error);
            alert('No se pudieron cargar los estados.');
        });
}

function openPopup(order) {
    document.getElementById('edit-id').value = order.OrderId;
    document.getElementById('edit-date').value = order.DateOrder;
    document.getElementById('edit-name').value = order.CustomerFullname;
    document.getElementById('edit-address').value = order.CustomerAdress;
    document.getElementById('edit-country').value = order.OrderCountry;
    document.getElementById('edit-products').value = order.ProductsOrdered;
    //document.getElementById('edit-status').value = order.OrderStatus;
    const editStatusSelect = document.getElementById('edit-status');
    for (let option of editStatusSelect.options) {
      if (option.value === order.OrderStatus) {
        option.selected = true;
        break;
      }
    }
    /*if (order.CustomerCity) {
        document.getElementById('edit-city').value = order.CustomerCity;
    }*/

  document.getElementById('order-popup').classList.remove('hidden');
}

function closePopup() {
  document.getElementById('order-popup').classList.add('hidden');
}

function updateOrder(e) {
    e.preventDefault();
  
    const orderId = document.getElementById('edit-id').value;
    const orderStatus = document.getElementById('edit-status').value;
  
    fetch('update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ OrderId: orderId, OrderStatus: orderStatus })
    })
    .then(res => res.text())
    .then(text => {
      console.log('Respuesta de update.php:', text);
      try {
        const res = JSON.parse(text);
        if (res.success) {
          closePopup();
          loadOrders();
          alert('Pedido actualizado correctamente.');
        } else {
          alert(res.error || 'Error al actualizar.');
        }
      } catch (err) {
        console.error('Error al parsear JSON:', err);
        alert('Respuesta no válida del servidor');
      }
    });
  }

function openNewOrderForm() {
  document.getElementById('new-order-popup').classList.remove('hidden');
}

function closeNewOrderForm() {
  document.getElementById('new-order-popup').classList.add('hidden');
}

function createOrder(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  fetch('insert.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      closeNewOrderForm();
      loadOrders();
      form.reset();
      alert('Pedido creado correctamente.');
    } else {
      alert(res.error || 'Error al crear pedido.');
    }
  });
}