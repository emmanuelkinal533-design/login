<template>
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
          {{ error }}
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

          <v-btn
            type="submit"
            color="primary"
            block
            size="large"
            :loading="cargando"
          >
            Entrar
          </v-btn>
        </v-form>
      </v-card>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const password = ref('');
const error = ref('');
const cargando = ref(false);
const mostrarPass = ref(false);

const iniciarSesion = async () => {
  error.value = '';
  cargando.value = true;

  try {
    await axios.post('/login', {
      email: email.value,
      password: password.value,
    });
    window.location.href = '/panel';
  } catch (err) {
    error.value = err.response?.data?.message || 'Correo o contraseña incorrectos.';
  } finally {
    cargando.value = false;
  }
};
</script>