<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro de Usuario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <v-app theme="dark">
            <v-main class="d-flex align-center justify-center" style="min-height: 100vh;">
                <v-card width="440" class="pa-6" elevation="8" rounded="lg">
                    <v-card-title class="text-h5 text-center font-weight-bold mb-1">
                        Registrar Nuevo Usuario
                    </v-card-title>
                    <v-card-subtitle class="text-center mb-4">
                        Completa los campos de la tabla users
                    </v-card-subtitle>

                    <v-alert v-if="error" type="error" variant="tonal" class="mb-4" density="compact">
                        @{{ error }}
                    </v-alert>

                    <v-alert v-if="exito" type="success" variant="tonal" class="mb-4" density="compact">
                        @{{ exito }}
                    </v-alert>
                    <v-form @submit.prevent="registrarUsuario">
                        <v-text-field
                            v-model="name"
                            label="Nombre"
                            prepend-inner-icon="mdi-account"
                            variant="outlined"
                            class="mb-2"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="email"
                            label="Correo electrónico"
                            type=   "email"
                            prepend-inner-icon="mdi-email"
                            variant="outlined"
                            class="mb-2"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="password"
                            label="Contraseña"
                            :type="mostrarPass ? 'text' : 'password'"
                            prepend-inner-icon="mdi-lock"
                            :append-inner-icon="mostrarPass ? 'mdi-eye-off' : 'mdi-eye'"
                            @click:append-inner="mostrarPass = !mostrarPass"
                            variant="outlined"
                            class="mb-2"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="password_confirmation"
                            label="Confirmar contraseña"
                            :type="mostrarPass ? 'text' : 'password'"
                            prepend-inner-icon="mdi-lock-check"
                            variant="outlined"
                            class="mb-4"
                            required
                        ></v-text-field>

                        <v-btn type="   submit" color="success" block size="large" :loading="cargando">
                            Registrarme
                        </v-btn>
                    </v-form>

                    <div class="text-center mt-4">
                        <v-btn variant="text" size="small" color="primary" href="/">
                            ¿Ya tienes cuenta? Volver al Login
                        </v-btn>
                    </div>
                </v-card>
            </v-main>
        </v-app>
    </div>

    <script type="module">
        const { createApp, ref } = window.Vue;

        createApp({
            setup() {
                const name = ref('');
                const email = ref('');
                const password = ref('');
                const password_confirmation = ref('');
                const error = ref('');
                const exito = ref('');
                const cargando = ref(false);
                const mostrarPass = ref(false);

                const registrarUsuario = async () => {
                    error.value = '';
                    exito.value = '';
                    cargando.value = true;

                    try {
                        await window.axios.post('/register', {
                            name: name.value,
                            email: email.value,
                            password: password.value,
                            password_confirmation: password_confirmation.value
                        });
                        exito.value = 'Usuario registrado correctamente. Redirigiendo al panel...';
                        setTimeout(() => {
                            window.location.href = '/panel';
                        }, 1000);
                    } catch (err) {
                        error.value = err.response?.data?.message || 'Error al registrar el usuario.';
                    } finally {
                        cargando.value = false;
                    }
                };

                return {
                    name, email, password, password_confirmation,
                    error, exito, cargando, mostrarPass, registrarUsuario
                };
            }
        }).use(window.createVuetify()).mount('#app');
    </script>
</body>
</html>