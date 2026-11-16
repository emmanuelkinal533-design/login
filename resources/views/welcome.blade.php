<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <v-app theme="dark">
            <v-main class="d-flex align-center justify-center" style="min-height: 100vh;">
                <v-card width="400" class="pa-6" elevation="8" rounded="lg">
                    <v-card-title class="text-h5 text-center font-weight-bold mb-1">
                        Iniciar Sesión
                    </v-card-title>
                    <v-card-subtitle class="text-center mb-4">
                        Ingresa tus credenciales
                    </v-card-subtitle>

                    <v-alert v-if="error" type="error" variant="tonal" class="mb-4" density="compact">
                        @{{ error }}
                    </v-alert>

                    <v-form @submit.prevent="iniciarSesion">
                        <v-text-field
                            v-model="email"
                            label="Correo electrónico"
                            type="email"
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
                            class="mb-4"
                            required
                        ></v-text-field>

                        <v-btn type="submit" color="primary" block size="large" :loading="cargando">
                            Entrar
                        </v-btn>
                    </v-form>

                    <v-divider class="my-4"></v-divider>

                    <v-btn href="/register" variant="outlined" color="success" block prepend-icon="mdi-account-plus">
                        registrarme
                    </v-btn>
                </v-card>
            </v-main>
        </v-app>
    </div>

    <script type="module">
        const { createApp, ref } = window.Vue;

        createApp({
            setup() {
                const email = ref('');
                const password = ref('');
                const error = ref('');
                const cargando = ref(false);
                const mostrarPass = ref(false);

                const iniciarSesion = async () => {
                    error.value = '';
                    cargando.value = true;
                    try {
                        await window.axios.post('/login', {
                            email: email.value,
                            password: password.value
                        });
                        window.location.href = '/panel';
                    } catch (err) {
                        error.value = err.response?.data?.message || 'Correo o contraseña incorrectos.';
                    } finally {
                        cargando.value = false;
                    }
                };

                return { email, password, error, cargando, mostrarPass, iniciarSesion };
            }
        }).use(window.createVuetify()).mount('#app');
    </script>
</body>
</html>