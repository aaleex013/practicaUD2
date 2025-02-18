document.addEventListener("DOMContentLoaded", () => { //espera a que cargue todo el html antes de ejecutar 
    const API_BASE_URL = "http://localhost:8000/api"; //define las variables
    let selectedController = "hello"; // //define las variables. hello porque es la primera

    const messageArea = document.getElementById("message-area");//donde se ven los mensajes
    const controllerButtons = document.querySelectorAll(".controller-btn");//botones hello,json y csv
    const actionButtons = document.querySelectorAll(".action-btn");//botones para hacer el crud

    // Seleccionar el controlador activo
    controllerButtons.forEach((button) => { //recorre lso 3 botones y asigna un evento
        button.addEventListener("click", () => {
            selectedController = button.getAttribute("data-controller");
            controllerButtons.forEach((btn) => btn.classList.remove("active"));
            button.classList.add("active");

            // Limpia el message-area cuando se cambia de controlador
            messageArea.textContent = "";
        
            logMessage(`Controlador seleccionado: ${selectedController}`);
        });
    });

    // Asignar eventos a los botones de acción
    actionButtons.forEach((button) => {
        button.addEventListener("click", async () => {
            const action = button.getAttribute("data-action");
            await handleAction(action);
        });
    });

    async function handleAction(action) {
        let endpoint = `${API_BASE_URL}/${selectedController}`; //construye la url del controller elegido
        let options = { method: "GET" };

        if (action === "getFiles") {
            endpoint += "/";
        } else if (action === "store") { //creas el archivo
            const { filename, content } = await promptForInput(
                "Crear Archivo", //titulo del modal
                "Nombre del archivo:", // label del primer campo 
                "Contenido:" // label del contenido
            );
            if (!filename || !content) return;
            options = {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ filename, content }),
            };
        } else if (action === "show") { //muestra el archivo elegido
            const filename = prompt("Introduce el nombre del archivo:");
            if (!filename) return;
            endpoint += `/${filename}`;
        } else if (action === "update") { //parecido a crear el archivo
            const { filename, content } = await promptForInput(
                "Actualizar Archivo",
                "Nombre del archivo:",
                "Nuevo contenido:"
            );
            if (!filename || !content) return;
            options = {
                method: "PUT", //actualiza el archivo
                headers: { "Content-Type": "application/json" },//tipo de dato
                body: JSON.stringify({ content }),//lo que queremos enviar
            };
            endpoint += `/${filename}`;
        } else if (action === "delete") { //lo borra
            const filename = prompt("Introduce el nombre del archivo a eliminar:");
            if (!filename) return;
            options = { method: "DELETE" };
            endpoint += `/${filename}`;
        }

        await fetchData(endpoint, options);//haces la solicitud http
    }

    async function fetchData(url, options = {}) {//funcion para la solicitud http
        logMessage(`Solicitando: ${url}...`);
        try {
            const response = await fetch(url, options);//se crea la solicitud
            if (!response.ok) throw new Error(`Error: ${response.status} ${response.statusText}`);
            const data = await response.json();
            logMessage(`Respuesta: ${JSON.stringify(data, null, 2)}`);
        } catch (error) {
            logMessage(`Error: ${error.message}`);
        }
    }

    function logMessage(message) {
        messageArea.style.display = "block"; // Mostrar el area de mensajes cuando se escribe en él
        messageArea.textContent += `\n${message}`;
        messageArea.scrollTop = messageArea.scrollHeight;//hace el scroll manualmente
    }

    function promptForInput(title, label1, label2) { //funcion que crea un modal para escribir el nombre y contenido del archivo
        return new Promise((resolve) => {
            const modalHtml = `
                <div id="modal" style="display: block; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
                    <div style="background-color: white; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 300px;">
                        <h2>${title}</h2>
                        <label>${label1}</label>
                        <input type="text" id="filenameInput" placeholder="${label1}">
                        <br><br>
                        <label>${label2}</label>
                        <textarea id="contentInput" placeholder="${label2}"style="width: 100%; height: 100px; padding: 5px;"></textarea>
                        <br><br>
                        <button id="submitModal">Enviar</button>
                        <button id="closeModal">Cerrar</button>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML("beforeend", modalHtml);//agrega el modal al body del html para que aparezca
            document.getElementById("submitModal").addEventListener("click", () => {//evento para que funcion el boton enviar
                const filename = document.getElementById("filenameInput").value;//escribes el nmbre del archivo
                const content = document.getElementById("contentInput").value;//escribe el contenido del archivo
                closeModal();
                resolve({ filename, content });//devuelve el objeto 
            });
            document.getElementById("closeModal").addEventListener("click", () => {//evento para que funcion el boton cerrar
                closeModal();
                resolve({});//cierra el modal y no devuelve datos
            });
        });
    }

    function closeModal() {//cierra el modal cuando envias el archivo o lo cierras
        document.getElementById("modal").remove();
    }
    
});
