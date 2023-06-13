<template>
    <AppLayout title="Usuarios">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Usuarios</div>
                    <div class="text-subtitle">Registro de los usuarios en el sistema a los que el administrador tiene acceso.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="usuarios.user_name" class="full-width"
                                    label="Nombre" :error-message="errores.user_name && errores.user_name[0]"
                                    :error="hayError(errores.user_name)" autofocus/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="usuarios.email"
                                    class="full-width" label="Correo electrónico"
                                    :error-message="errores.email && errores.email[0]"
                                    :error="hayError(errores.email)" />
                            </q-item>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input type="password" filled bottom-slots v-model="usuarios.password" class="full-width"
                                    label="Contraseña" :error-message="errores.password && errores.password[0]"
                                    :error="hayError(errores.password)"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input type="password" filled bottom-slots v-model="usuarios.confirmarContraseña" class="full-width"
                                label="Confirmar Contraseña" :error-message="errores.confirmarContraseña && errores.confirmarContraseña[0]"
                                :error="hayError(errores.confirmarContraseña)"/>
                            </q-item>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <div class="q-pa-md">
            <q-table flat bordered :rows="detalleTabla" :columns="columns" row-key="id" v-model:pagination="pagination"
                :loading="loading" :filter="filter" binary-state-sort :rows-per-page-options="[5, 10, 20, 40, 0]"
                @request="generarTabla">
                <template v-slot:top-right>
                    <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                <template v-slot:top-left>
                    <div class="q-gutter-sm">
                        <q-btn outline rounded color="primary" label="Guardar" icon="add" @click="guardar"></q-btn>
                        <q-btn outline rounded color="danger" label="Cancelar" icon="cancel" @click="cancelar"></q-btn>
                    </div>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <div class="q-gutter-sm">
                            <q-btn round color="warning" icon="edit" class="mr-2" @click="editar(props.row, $page.props.auth.user.id)"></q-btn>
                        </div>
                    </q-td>
                </template>
            </q-table>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useQuasar } from 'quasar'
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

/* VARIABLES Y CONSTANTES, con camelPascal */
// De quasar
const $q = useQuasar() // Para mensajes de exito o error

// De la vista
const detalleTabla = ref()
const submitted = ref(false) // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false)
const usuarios = ref({}) // El objeto que se enviara mediante el request
const confirmarEliminacion = ref(false) // Para modal de eliminacion
const nombreRegistroEliminar = ref('') // Para que se muestre el nombre en el modal de eliminacion

// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}) // Para almacenar el array de errores que viene desde Laravel

// Para el q-table con server-rendering
// Fijos e imperativos que no se tocan
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    page: 1,
    rowsPerPage: 5,
    /* Cuando se usa server side pagination, QTable necesita
    conocer el "rowsNumber" (Numero total de filas).
    Por qué?
    Porque Quasar no tiene forma de saber cuál será
    la última página sin esta información!
    Por lo tanto, ahora debemos proporcionarle un "número de filas" nosotros mismos.. */
    rowsNumber: 0
})
// Fin de fijos e imperativos

// Definiendo las columnas que contendra la tabla. Esto es customizable
const columns = [
    { name: 'id', align: 'left', label: 'Identificador', field: 'id', sortable: true },
    { name: 'user_name', align: 'left', label: 'Nombre', field: 'user_name', sortable: true },
    { name: 'email', align: 'left', label: 'Correo', field: 'email', sortable: true },
    { name: 'email_verified', align: 'left', label: 'Estado Correo', field: 'email_verified', sortable: true },
    { name: 'operaciones', align: 'center', label: 'Edición' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    usuarios.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

const cancelar = () => {
    usuarios.value = {}

}


// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
    if (submitted && valor)
        return true
    else
        return false
}

// Operacion de guardar
const guardar = async () => {
    submitted.value = true
    errores.value = {}

    // Verificar si los datos son iguales al usuario seleccionado
    if (usuarios.value.id) {
        const usuarioSeleccionado = detalleTabla.value.find(user => user.id === usuarios.value.id)
        if (usuarioSeleccionado) {
            if (usuarioSeleccionado.user_name === usuarios.value.user_name &&
                usuarioSeleccionado.email === usuarios.value.email &&
                usuarioSeleccionado.password === usuarios.value.password &&
                usuarioSeleccionado.confirmarContraseña === usuarios.value.confirmarContraseña) {
                $q.notify({
                    type: 'info',
                    message: 'No se realizaron cambios en los datos del usuario.',
                    color: 'secondary'
                })
                return;
            }
        }
    }


    // Actualizar
    if (usuarios.value.id) {
        await axios
            .post("/api/usuarios/actualizar", usuarios.value)
            .then((response) => {
                reiniciarValores()
                if (response.data.message === 'El usuario no sufrió cambios') {
                    $q.notify({
                    type: 'info',
                    message: 'El usuario no sufrió cambios',
                    color: 'secondary' // Cambiar el color de la notificación
                })
                return;
            }
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Usuario actualizado.'
                    }
                )

            })
            .catch((e) => {
                
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors;
                    // Mensaje de alerta para error 422 - Datos improsesables
                    $q.notify({
                    type: 'negative',
                    message: 'Error al actualizar el usuario.'
                    });
                } else if (e.response.status === 409) {
                    errores.value = e.response.data.errors;
                    // Mensaje de alerta para error 409 - Error de conflicto (por que ya existe el rol)
                    $q.notify({
                    type: 'negative',
                    message: 'No puede actualizar los tres campos al mismo tiempo.'
                    });
                } else {
                    // Mensaje de alerta genérico en caso de otros errores
                    $q.notify({
                    type: 'negative',
                    message: 'Error al actualizar el usuario.'
                    });
                }
            })
    }
    // Guardar
    else {
        await axios
            .post("/api/usuarios/agregar", usuarios.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Usuario guardado.'
                    }
                )

            })
            .catch((e) => {
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors
                }
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'negative',
                        message: 'Error al agregar el usuario.'
                    }
                )
            })
    }
}
// Para mostrar los datos en el form
const editar = (editarUsuarios, user_active_id) => {
    if(user_active_id == editarUsuarios.id){
        $q.notify(
            {
                type: 'negative',
                message: 'No te puedes editar a ti mismo.'
            }
        )
        return;
    }
    usuarios.value = { ...editarUsuarios }
    submitted.value = false;
    errores.value = {};
}

/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage } = props.pagination
    const filter = props.filter
    loading.value = true
    // Obteniendo la tabla de datos
    await axios
        .get("/api/usuarios/tabla", {
            params: {
                page,
                rowsPerPage,
                filter
            }
        })
        .then(response => {
            detalleTabla.value = response.data.detalle
            // Actualizando el objeto de paginación local. Solamente se cambia la info del response data de ser necesario
            pagination.value.page = response.data.paginacion.pagina
            pagination.value.rowsPerPage = response.data.paginacion.filasPorPagina
            pagination.value.rowsNumber = response.data.paginacion.tuplas
        })
        .catch(error => {
            errored.value = true
        })

    // Apagando el indicador de carga. Este no se toca
    loading.value = false
}

</script>
