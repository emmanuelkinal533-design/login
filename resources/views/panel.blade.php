<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Principal - Vue & Vuetify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <v-app :theme="temaOscuro ? 'dark' : 'light'">
            <v-navigation-drawer v-model="drawer" permanent>
                <v-list-item
                    title="{{ auth()->user()->name }}"
                    subtitle="{{ auth()->user()->email }}"
                    class="py-3"
                >resources/views/panel.blade.php
                     <template v-slot:prepend>
                        <v-avatar color="primary">
                            <v-icon icon="mdi-account"></v-icon>
                        </v-avatar>
                    </template>
                </v-list-item>

                <v-divider></v-divider>

                <v-list density="compact" nav>
                    <v-list-item
                        prepend-icon="mdi-view-dashboard"
                        title="Dashboard"
                        :active="vistaActual === 'dashboard'"
                        @click="vistaActual = 'dashboard'"
                        color="primary"
                    ></v-list-item>
                    <v-list-item
                        prepend-icon="mdi-account-group"
                        title="Usuarios Registrados"
                        :active="vistaActual === 'usuarios'"
                        @click="vistaActual = 'usuarios'"
                        color="primary"
                    ></v-list-item>
                </v-list>
            </v-navigation-drawer>

            <v-app-bar elevation="2">
                <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
                <v-app-bar-title>Sistema Administrativo</v-app-bar-title>
                <v-spacer></v-spacer>
                <v-btn
                    :icon="temaOscuro ? 'mdi-weather-sunny' : 'mdi-weather-night'"
                    @click="temaOscuro = !temaOscuro"
                    class="mr-2"
                ></v-btn>
                <v-btn color="error" variant="tonal" prepend-icon="mdi-logout" @click="cerrarSesion">
                    Salir
                </v-btn>
            </v-app-bar>

            <v-main>
                <v-container fluid class="pa-6">
                    <v-row class="mb-4">
                        <v-col cols="12" sm="4">
                            <v-card elevation="3" rounded="lg" class="pa-4">
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <div class="text-overline text-medium-emphasis">Usuarios en BD</div>
                                        <div class="text-h4 font-weight-bold">@{{ usuarios.length }}</div>
                                    </div>
                                    <v-avatar color="primary" variant="tonal" size="48">
                                        <v-icon icon="mdi-account-multiple" size="28"></v-icon>
                                    </v-avatar>
                                </div>
                            </v-card>
                        </v-col>

                        <v-col cols="12" sm="4">
                            <v-card elevation="3" rounded="lg" class="pa-4">
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <div class="text-overline text-medium-emphasis">Sesión Activa</div>
                                        <div class="text-h6 font-weight-bold">{{ auth()->user()->name }}</div>
                                    </div>
                                    <v-avatar color="success" variant="tonal" size="48">
                                        <v-icon icon="mdi-shield-check" size="28"></v-icon>
                                    </v-avatar>
                                </div>
                            </v-card>
                        </v-col>

                        <v-col cols="12" sm="4">
                            <v-card elevation="3" rounded="lg" class="pa-4">
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <div class="text-overline text-medium-emphasis">Modo de Operación</div>
                                        <div class="text-h6 font-weight-bold">100% Local / Offline</div>
                                    </div>
                                    <v-avatar color="info" variant="tonal" size="48">
                                        <v-icon icon="mdi-vuejs" size="28"></v-icon>
                                    </v-avatar>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-card elevation="3" rounded="lg">
                        <v-card-title class="d-flex align-center pa-4">
                            <v-icon icon="mdi-database" class="mr-2" color="primary"></v-icon>
                            Usuarios en Base de Datos
                            <v-spacer></v-spacer>
                            <v-text-field
                                v-model="busqueda"
                                prepend-inner-icon="mdi-magnify"
                                label="Buscar usuario..."
                                single-line
                                hide-details
                                density="compact"
                                variant="outlined"
                                style="max-width: 280px;"
                            ></v-text-field>
                        </v-card-title>

                        <v-data-table
                            :headers="columnas"
                            :items="usuarios"
                            :search="busqueda"
                            :loading="cargando"
                            items-per-page="5"
                        >
                            <template v-slot:item.created_at="{ item }">
                                @{{ new Date(item.created_at).toLocaleDateString() }}
                            </template>

                            <template v-slot:item.acciones="{ item }">
                                <v-btn
                                    icon="mdi-delete"
                                    size="small"
                                    color="error"
                                    variant="text"
                                    @click="eliminarUsuario(item.id)"
                                ></v-btn>
                            </template>
                        </v-data-table>
                    </v-card>

                    <v-snackbar v-model="snackbar.visible" :color="snackbar.color" timeout="3000">
                        @{{ snackbar.texto }}
                    </v-snackbar>
                </v-container>
            </v-main>
        </v-app>
    </div>

    <script type="module">
        const { createApp, ref, onMounted } = window.Vue;

        createApp({
            setup() {
                const drawer = ref(true);
                const temaOscuro = ref(true);
                const vistaActual = ref('dashboard');
                const usuarios = ref([]);
                const busqueda = ref('');
                const cargando = ref(false);
                const snackbar = ref({ visible: false, texto: '', color: 'success' });

                const columnas = [
                    { title: 'ID', key: 'id' },
                    { title: 'Nombre', key: 'name' },
                    { title: 'Correo Electrónico', key: 'email' },
                    { title: 'Fecha Registro', key: 'created_at' },
                    { title: 'Acciones', key: 'acciones', sortable: false },
                ];

                const mostrarAlerta = (texto, color = 'success') => {
                    snackbar.value = { visible: true, texto, color };
                };

                const cargarUsuarios = async () => {
                    cargando.value = true;
                    try {
                        const res = await window.axios.get('/api/usuarios');
                        usuarios.value = res.data;
                    } catch (e) {
                        mostrarAlerta('Error al cargar usuarios', 'error');
                    } finally {
                        cargando.value = false;
                    }
                };

                const eliminarUsuario = async (id) => {
                    try {
                        await window.axios.delete(`/api/usuarios/${id}`);
                        mostrarAlerta('Usuario eliminado correctamente');
                        cargarUsuarios();
                    } catch (err) {
                        mostrarAlerta(err.response?.data?.message || 'No se pudo eliminar', 'error');
                    }
                };

                const cerrarSesion = async () => {
                    await window.axios.post('/logout');
                    window.location.href = '/';
                };

                onMounted(() => {
                    cargarUsuarios();
                });

                return {
                    drawer, temaOscuro, vistaActual, usuarios, busqueda,
                    cargando, columnas, snackbar, eliminarUsuario, cerrarSesion
                };
            }
        }).use(window.createVuetify()).mount('#app');
    </script>
</body>
</html>