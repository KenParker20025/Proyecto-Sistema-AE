document.addEventListener('DOMContentLoaded', function() {
    const addDiarioButton = document.getElementById('add-diario-button');
    const diarioWrapper = document.getElementById('diario-wrapper');

    fetchDiarios();

    addDiarioButton.addEventListener('click', function() {
        const newDiario = createDiarioElement({
            id: null,
            titulo: 'Nuevo Diario de Campo',
            archivo: 'diario-image.jpg',
            fecha: new Date().toISOString().split('T')[0]
        });
        diarioWrapper.appendChild(newDiario);
    });

    function fetchDiarios() {
        fetch('diario_campo.php', {
            method: 'POST',
            body: new URLSearchParams('action=fetch')
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(diario => {
                const diarioElement = createDiarioElement(diario);
                diarioWrapper.appendChild(diarioElement);
            });
        });
    }

    function createDiarioElement(diario) {
        const newDiario = document.createElement('div');
        newDiario.className = 'diario-container';
        newDiario.innerHTML = `
            <button class="back-button">⮐</button>
            <h1>Diario de Campo</h1>
            <div class="diario-content">
                <h2 class="diario-title">${diario.titulo}</h2>
                <button class="edit-button">Editar</button>
                <button class="share-button">Compartir</button>
                <div class="diario-entry">
                    <img src="${diario.archivo}" alt="Diario de Campo">
                    <div class="actions">
                        <button class="download-button">⬇</button>
                        <button class="print-button">🖨</button>
                    </div>
                </div>
                <div class="dropzone">
                    <p>Arrastra y suelta documentos aquí (PDF, Word, PowerPoint)</p>
                </div>
            </div>
        `;

        attachEventHandlers(newDiario, diario.id);
        initializeDropzone(newDiario.querySelector('.dropzone'));

        return newDiario;
    }

    function attachEventHandlers(container, id) {
        const backButton = container.querySelector('.back-button');
        const editButton = container.querySelector('.edit-button');
        const shareButton = container.querySelector('.share-button');
        const downloadButton = container.querySelector('.download-button');
        const printButton = container.querySelector('.print-button');
        const title = container.querySelector('.diario-title');

        backButton.addEventListener('click', function() {
            alert('Regresar a la página anterior');
        });

        editButton.addEventListener('click', function() {
            if (!editButton.classList.contains('editing')) {
                const currentTitle = title.textContent;
                const input = document.createElement('input');
                input.type = 'text';
                input.value = currentTitle;
                input.className = 'title-input';
                title.replaceWith(input);
                input.focus();
                editButton.textContent = 'Guardar';
                editButton.classList.add('editing');
                input.addEventListener('blur', function() {
                    saveTitle(input, title, editButton, id);
                });
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        saveTitle(input, title, editButton, id);
                    }
                });
            } else {
                const input = container.querySelector('.title-input');
                saveTitle(input, title, editButton, id);
            }
        });

        shareButton.addEventListener('click', function() {
            alert('Compartir la entrada del diario');
        });

        downloadButton.addEventListener('click', function() {
            alert('Descargar la imagen del diario');
        });

        printButton.addEventListener('click', function() {
            alert('Imprimir la entrada del diario');
        });
    }

    function saveTitle(input, title, editButton, id) {
        const newTitle = input.value;
        const newTitleElement = document.createElement('h2');
        newTitleElement.className = 'diario-title';
        newTitleElement.textContent = newTitle;
        input.replaceWith(newTitleElement);
        editButton.textContent = 'Editar';
        editButton.classList.remove('editing');

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', id);
        formData.append('titulo', newTitle);
        formData.append('archivo', 'diario-image.jpg'); // Asumiendo que la imagen es la misma
        formData.append('fecha', new Date().toISOString().split('T')[0]);

        fetch('diario_campo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
        });
    }

    function initializeDropzone(dropzone) {
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', function() {
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');

            const files = e.dataTransfer.files;
            for (const file of files) {
                if (file.type === 'application/pdf' || 
                    file.type === 'application/msword' || 
                    file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
                    file.type === 'application/vnd.ms-powerpoint' || 
                    file.type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
                    Swal.fire('Archivo válido cargado', file.name, 'success');
                    dropzone.innerHTML = `<p>Archivo cargado: ${file.name}</p>`;
                    dropzone.removeEventListener('dragover');
                    dropzone.removeEventListener('dragleave');
                    dropzone.removeEventListener('drop');
                } else {
                    Swal.fire('Archivo no válido', file.name, 'error');
                }
            }
        });
    }

    const existingDropzones = document.querySelectorAll('.dropzone');
    existingDropzones.forEach(initializeDropzone);
    attachEventHandlers(document.querySelector('.diario-container'));
});
