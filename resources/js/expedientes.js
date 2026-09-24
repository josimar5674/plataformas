document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('.subject-create-form');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');

        button.disabled = true;
        button.textContent = 'Guardando...';

        try {

            const response = await fetch(form.action, {

                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN':
                        document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),

                    'Accept': 'application/json',
                },

                body: new FormData(form),

            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || 'No se pudo guardar el sujeto.'
                );
            }

          console.log('RESPUESTA COMPLETA:', data);
console.log('SUJETO:', data.sujeto);

form.reset();

const lista = document.getElementById('lista-sujetos');


console.log('LISTA:', lista);

const sujeto = data.sujeto;

// Quitar mensaje de "No hay sujetos registrados"
const mensajeVacio = lista.querySelector('.sujetos-empty');

if (mensajeVacio) {
    mensajeVacio.remove();
}

const item = document.createElement('div');

item.className = 'exp-list-item';

item.innerHTML = `
 <strong class="sujeto-tipo">
    ${sujeto.tipo.toUpperCase()}
</strong>

<div class="sujeto-nombre">
    ${sujeto.nombre}
</div>

<div class="exp-muted">

    <span class="sujeto-identificacion">
        ID: ${sujeto.identificacion || '—'}
    </span>

    |

    <span class="sujeto-cah">
        CAH: ${sujeto.cah || '—'}
    </span>

</div>

    <div style="display:flex; gap:8px; margin-top:10px;">

        <button
            type="button"
            class="exp-btn exp-btn-yellow"
            onclick="toggleForm('editar-sujeto-${sujeto.id}')">

            ✏️ Editar

        </button>

        <form
            method="POST"
            action="/sujetos/${sujeto.id}"
            onsubmit="return confirm('¿Está seguro que desea eliminar este sujeto?');">

            <input
                type="hidden"
                name="_token"
                value="${document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')}">

            <input
                type="hidden"
                name="_method"
                value="DELETE">

            <button
                type="submit"
                class="exp-btn exp-btn-red">

                🗑️ Eliminar

            </button>

        </form>

    </div>

    <div
        id="editar-sujeto-${sujeto.id}"
        class="hidden"
        style="margin-top:12px;">

        <form
            method="POST"
            action="/sujetos/${sujeto.id}">

            <input
                type="hidden"
                name="_token"
                value="${document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')}">

            <input
                type="hidden"
                name="_method"
                value="PUT">

            <div class="exp-form-grid">

                <select
                    name="tipo"
                    class="exp-select">

                    <option value="sujeto activo"
                        ${sujeto.tipo === 'sujeto activo' ? 'selected' : ''}>
                        Sujeto Activo
                    </option>

                    <option value="sujeto pasivo"
                        ${sujeto.tipo === 'sujeto pasivo' ? 'selected' : ''}>
                        Sujeto Pasivo
                    </option>

                    <option value="apoderado activo"
                        ${sujeto.tipo === 'apoderado activo' ? 'selected' : ''}>
                        Apoderado Activo
                    </option>

                    <option value="apoderado pasivo"
                        ${sujeto.tipo === 'apoderado pasivo' ? 'selected' : ''}>
                        Apoderado Pasivo
                    </option>

                </select>

                <input
                    type="text"
                    name="nombre"
                    value="${sujeto.nombre || ''}"
                    placeholder="Nombre"
                    required
                    class="exp-input">

                <input
                    type="text"
                    name="identificacion"
                    value="${sujeto.identificacion || ''}"
                    placeholder="Identificación"
                    class="exp-input">

                <input
                    type="text"
                    name="cah"
                    value="${sujeto.cah || ''}"
                    placeholder="CAH"
                    class="exp-input">

            </div>

            <div style="display:flex; gap:8px; margin-top:10px;">

                <button
                    type="submit"
                    class="exp-btn exp-btn-blue">

                    💾 Guardar cambios

                </button>

                <button
                    type="button"
                    onclick="toggleForm('editar-sujeto-${sujeto.id}')"
                    class="exp-btn exp-btn-gray">

                    Cancelar

                </button>

            </div>

        </form>

    </div>
`;

lista.prepend(item);

           

        } catch (error) {

            console.error(error);

            alert(error.message);

        } finally {

            button.disabled = false;
            button.textContent = 'Guardar Sujeto';

        }

    });

});


document.addEventListener('submit', async (event) => {

    const form = event.target.closest('.subject-edit-form');

    if (!form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('button[type="submit"]');

    button.disabled = true;
    button.textContent = 'Guardando...';

    try {

        const response = await fetch(form.action, {

            method: 'POST',

            headers: {
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),

                'Accept': 'application/json',
            },

            body: new FormData(form),

        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message || 'No se pudo actualizar el sujeto.'
            );
        }

        console.log('Sujeto actualizado:', data.sujeto);

        const sujeto = data.sujeto;

        const item = form.closest('.exp-list-item');

        if (item) {

            item.querySelector('.sujeto-tipo').textContent =
                sujeto.tipo.toUpperCase();

            item.querySelector('.sujeto-nombre').textContent =
                sujeto.nombre;

            item.querySelector('.sujeto-identificacion').textContent =
                `ID: ${sujeto.identificacion || '—'}`;

            item.querySelector('.sujeto-cah').textContent =
                `CAH: ${sujeto.cah || '—'}`;
        }

        toggleForm(`editar-sujeto-${sujeto.id}`);

    } catch (error) {

        console.error(error);

        alert(error.message);

    } finally {

        button.disabled = false;
        button.textContent = '💾 Guardar cambios';

    }

});