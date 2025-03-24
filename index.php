<!-- index.php -->
<?php include 'conndb.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Freshly - Gestor de Pedidos</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
    <img src="img/freshlyLogo.png" alt="Freshly Cosmetics" style="height: 60px;" />
    <h1>Gestión de Pedidos</h1>
  </div>
  <div class="filters">
  <button onclick="openNewOrderForm()">Nuevo Pedido</button>
    <label>País:
      <select id="filter-country"></select>
    </label>
    <label>Estado:
      <select id="filter-status">
        <option value="">Todos</option>
        <!--<option value="Pago pendiente">Pago pendiente</option>
        <option value="Enviado">Enviado</option>
        <option value="Entregado">Entregado</option>-->
      </select>
    </label>
    <!--<button onclick="loadOrders()">Filtrar</button>--> <!-- Botón para filtros de prueba -->
  </div>

  <!--<button onclick="openNewOrderForm()">Nuevo Pedido</button>-->

  <table id="orders-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Nombre</th>
        <th>Dirección</th>
        <th>País</th>
        <th>Productos</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <div id="order-popup" class="modal hidden">
    <div class="modal-content">
      <span class="close" onclick="closePopup()">&times;</span>
      <form id="edit-form">
        <h2>Editar Estado del Pedido</h2>
        <label>ID: <input type="text" id="edit-id" readonly></label>
        <label>Fecha: <input type="text" id="edit-date" readonly></label>
        <label>Nombre: <input type="text" id="edit-name" readonly></label>
        <label>Dirección: <input type="text" id="edit-address" readonly></label>
        <label>Ciudad: <input type="text" id="edit-city" readonly></label>
        <label>País: <input type="text" id="edit-country" readonly></label>
        <label>Productos: <input type="text" id="edit-products" readonly></label>
        <label>Estado:
          <select id="edit-status">
            <!--<option value="Pendiente">Pendiente</option>
            <option value="Enviado">Enviado</option>
            <option value="Entregado">Entregado</option>-->
          </select>
        </label>
        <button type="submit">Guardar cambios</button>
      </form>
    </div>
  </div>

  <div id="new-order-popup" class="modal hidden">
    <div class="modal-content">
      <span class="close" onclick="closeNewOrderForm()">&times;</span>
      <form id="new-form">
        <h2>Nuevo Pedido</h2>
        <label>Fecha: <input type="date" name="date" required></label>
        <label>Nombre: <input type="text" name="name" required></label>
        <label>Apellidos: <input type="text" name="lastname" required></label>
        <label>Dirección: <input type="text" name="address" required></label>
        <label>País: <input type="text" name="country" required></label>
        <label>Productos: <input type="text" name="products" required></label>
        <label>Estado:
          <select name="status">
            <!--<option value="Pendiente">Pendiente</option>
            <option value="Enviado">Enviado</option>
            <option value="Entregado">Entregado</option>-->
          </select>
        </label>
        <button type="submit">Crear Pedido</button>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
  <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>