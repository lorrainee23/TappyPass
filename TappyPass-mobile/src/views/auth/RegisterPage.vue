<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/login"></ion-back-button>
        </ion-buttons>
        <ion-title>Register</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="ion-padding">
      <div class="register-container">
        <form @submit.prevent="handleRegister">
          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="formData.name"
              type="text"
              label="Full Name"
              label-placement="floating"
              required
              autocomplete="name"
            ></ion-input>
          </ion-item>

          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="formData.email"
              type="email"
              label="Email"
              label-placement="floating"
              required
              autocomplete="email"
            ></ion-input>
          </ion-item>

          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="formData.phone"
              type="tel"
              label="Phone Number"
              label-placement="floating"
              autocomplete="tel"
            ></ion-input>
          </ion-item>

          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="formData.password"
              type="password"
              label="Password"
              label-placement="floating"
              required
              autocomplete="new-password"
            ></ion-input>
          </ion-item>

          <ion-item lines="full" class="input-item">
            <ion-input
              v-model="formData.password_confirmation"
              type="password"
              label="Confirm Password"
              label-placement="floating"
              required
              autocomplete="new-password"
            ></ion-input>
          </ion-item>

          <ion-button
            expand="block"
            type="submit"
            class="register-button"
            :disabled="loading"
          >
            <ion-spinner v-if="loading" name="crescent"></ion-spinner>
            <span v-else>Create Account</span>
          </ion-button>

          <div class="login-link">
            <p>
              Already have an account?
              <router-link to="/login">Login here</router-link>
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
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonItem,
  IonLabel,
  IonInput,
  IonButton,
  IonButtons,
  IonBackButton,
  IonSpinner,
  toastController,
} from '@ionic/vue';
import { authService } from '@/services/auth';

const router = useRouter();
const loading = ref(false);
const formData = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
});

const handleRegister = async () => {
  if (formData.value.password !== formData.value.password_confirmation) {
    const toast = await toastController.create({
      message: 'Passwords do not match',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
    return;
  }

  loading.value = true;
  try {
    await authService.register(formData.value);
    
    // Clear form
    formData.value = {
      name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: '',
    };
    
    const toast = await toastController.create({
      message: 'Account created successfully!',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();
    
    // Force reload to refresh user data across all components
    window.location.href = '/tabs/home';
  } catch (error: any) {
    const toast = await toastController.create({
      message: error.response?.data?.message || 'Registration failed. Please try again.',
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
.register-container {
  max-width: 400px;
  margin: 0 auto;
  padding-top: 20px;
}

.input-item {
  margin-bottom: 16px;
  --background: var(--ion-color-light);
  border-radius: 8px;
}

.register-button {
  margin-top: 24px;
  height: 48px;
  font-weight: 600;
}

.login-link {
  text-align: center;
  margin-top: 24px;
}

.login-link a {
  color: var(--ion-color-primary);
  text-decoration: none;
  font-weight: 600;
}
</style>
