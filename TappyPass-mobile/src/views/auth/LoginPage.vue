<template>
  <ion-page>
    <ion-content :fullscreen="true" class="ion-padding">
      <div class="login-container">
        <div class="logo-section">
          <ion-icon :icon="busOutline" class="logo-icon"></ion-icon>
          <h1>TappyPass</h1>
          <p>Bus Booking Made Easy</p>
        </div>

        <form @submit.prevent="handleLogin">
          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="credentials.email"
              type="email"
              label="Email"
              label-placement="floating"
              required
              autocomplete="email"
            ></ion-input>
          </ion-item>

          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="credentials.password"
              type="password"
              label="Password"
              label-placement="floating"
              required
              autocomplete="current-password"
            ></ion-input>
          </ion-item>

          <ion-button
            expand="block"
            type="submit"
            class="login-button"
            :disabled="loading"
          >
            <ion-spinner v-if="loading" name="crescent"></ion-spinner>
            <span v-else>Login</span>
          </ion-button>

          <div class="register-link">
            <p>
              Don't have an account?
              <router-link to="/register">Register here</router-link>
            </p>
          </div>
        </form>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonContent,
  IonItem,
  IonLabel,
  IonInput,
  IonButton,
  IonIcon,
  IonSpinner,
  toastController,
} from '@ionic/vue';
import { busOutline } from 'ionicons/icons';
import { authService } from '@/services/auth';

const router = useRouter();
const loading = ref(false);
const credentials = ref({
  email: '',
  password: '',
});

const handleLogin = async () => {
  loading.value = true;
  try {
    await authService.login(credentials.value);
    router.push('/tabs/home');
  } catch (error: any) {
    const toast = await toastController.create({
      message: error.response?.data?.message || 'Login failed. Please check your credentials.',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 0 auto;
  padding-top: 60px;
}

.logo-section {
  text-align: center;
  margin-bottom: 40px;
}

.logo-icon {
  font-size: 80px;
  color: var(--ion-color-primary);
  margin-bottom: 16px;
}

.logo-section h1 {
  font-size: 32px;
  font-weight: bold;
  margin: 0;
  color: var(--ion-color-primary);
}

.logo-section p {
  color: var(--ion-color-medium);
  margin-top: 8px;
}

.input-item {
  margin-bottom: 16px;
  --background: var(--ion-color-light);
  border-radius: 8px;
}

.login-button {
  margin-top: 24px;
  height: 48px;
  font-weight: 600;
}

.register-link {
  text-align: center;
  margin-top: 24px;
}

.register-link a {
  color: var(--ion-color-primary);
  text-decoration: none;
  font-weight: 600;
}
</style>
