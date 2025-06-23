// scripts.js

document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('tableContainer');
    const addTableBtn = document.getElementById('addTableBtn');

    addTableBtn.addEventListener('click', function() {
        addNewTable();
    });

    function addNewTable() {
        const newTableId = `table${document.querySelectorAll('.table').length + 1}`;
        const tableHtml = `
            <div class="table" id="${newTableId}">
                <div class="table-header">
                    <span>Nuevo apartado</span>
                    <button class="edit-table-btn">Editar</button>
                    <button class="delete-table-btn">Eliminar</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><input type="file"></td>
                            <td><input type="date"></td>
                            <td>
                                <button class="view-btn">👁️</button>
                                <button class="edit-row-btn">✏️</button>
                                <button class="save-row-btn">💾</button>
                                <button class="delete-row-btn">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="add-row-btn">➕</button>
            </div>
        `;

        tableContainer.insertAdjacentHTML('beforeend', tableHtml);

        addTableEventListeners(document.getElementById(newTableId));
    }

    function addTableEventListeners(table) {
        const editTableBtn = table.querySelector('.edit-table-btn');
        const deleteTableBtn = table.querySelector('.delete-table-btn');
        const addRowBtn = table.querySelector('.add-row-btn');

        editTableBtn.addEventListener('click', function() {
            editTableName(table);
        });

        deleteTableBtn.addEventListener('click', function() {
            deleteTable(table);
        });

        addRowBtn.addEventListener('click', function() {
            addNewRow(table);
        });

        table.querySelectorAll('.edit-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                editRow(btn.closest('tr'));
            });
        });

        table.querySelectorAll('.save-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                saveRow(btn.closest('tr'));
            });
        });

        table.querySelectorAll('.delete-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteRow(btn.closest('tr'));
            });
        });

        table.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                viewFile(btn.closest('tr'));
            });
        });
    }

    function editTableName(table) {
        Swal.fire({
            title: 'Editar nombre de la tabla',
            input: 'text',
            inputLabel: 'Nuevo nombre',
            inputPlaceholder: 'Introduce el nuevo nombre de la tabla',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                table.querySelector('.table-header span').textContent = result.value;
            }
        });
    }

    function deleteTable(table) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                table.remove();
                Swal.fire('¡Eliminado!', 'La tabla ha sido eliminada.', 'success');
            }
        });
    }

    function addNewRow(table) {
        const newRowId = table.querySelectorAll('tbody tr').length + 1;
        const newRowHtml = `
            <tr>
                <td>${newRowId}</td>
                <td><input type="file"></td>
                <td><input type="date"></td>
                <td>
                    <button class="view-btn">👁️</button>
                    <button class="edit-row-btn">✏️</button>
                    <button class="save-row-btn">💾</button>
                    <button class="delete-row-btn">🗑️</button>
                </td>
            </tr>
        `;

        table.querySelector('tbody').insertAdjacentHTML('beforeend', newRowHtml);

        const newRow = table.querySelector(`tbody tr:last-child`);

        newRow.querySelector('.edit-row-btn').addEventListener('click', function() {
            editRow(newRow);
        });

        newRow.querySelector('.save-row-btn').addEventListener('click', function() {
            saveRow(newRow);
        });

        newRow.querySelector('.delete-row-btn').addEventListener('click', function() {
            deleteRow(newRow);
        });

        newRow.querySelector('.view-btn').addEventListener('click', function() {
            viewFile(newRow);
        });
    }

    function editRow(row) {
        row.querySelector('input[type="file"]').disabled = false;
        row.querySelector('input[type="date"]').disabled = false;
    }

    function saveRow(row) {
        row.querySelector('input[type="file"]').disabled = true;
        row.querySelector('input[type="date"]').disabled = true;
        Swal.fire('Guardado!', 'La fila ha sido guardada.', 'success');
    }

    function deleteRow(row) {
        row.remove();
    }

    function viewFile(row) {
        const fileInput = row.querySelector('input[type="file"]');
        if (fileInput.files.length === 0) {
            Swal.fire('Error', 'No hay archivo para mostrar.', 'error');
        } else {
            const file = fileInput.files[0];
            const fileUrl = URL.createObjectURL(file);
            window.open(fileUrl, '_blank');
        }
    }

    // Inicializar event listeners para la tabla existente
    document.querySelectorAll('.table').forEach(table => {
        addTableEventListeners(table);
    });
});