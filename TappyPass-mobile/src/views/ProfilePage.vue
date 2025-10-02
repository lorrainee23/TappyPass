<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Profile</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Profile</ion-title>
        </ion-toolbar>
      </ion-header>

      <div class="ion-padding">
        <div class="profile-header">
          <div class="avatar">
            <ion-icon :icon="personCircleOutline"></ion-icon>
          </div>
          <h2>{{ user?.name }}</h2>
          <p>{{ user?.email }}</p>
        </div>

        <ion-list>
          <ion-list-header>
            <ion-label>Account Information</ion-label>
          </ion-list-header>

          <ion-item>
            <ion-icon slot="start" :icon="personOutline"></ion-icon>
            <ion-label>
              <h3>Full Name</h3>
              <p>{{ user?.name }}</p>
            </ion-label>
          </ion-item>

          <ion-item>
            <ion-icon slot="start" :icon="mailOutline"></ion-icon>
            <ion-label>
              <h3>Email</h3>
              <p>{{ user?.email }}</p>
            </ion-label>
          </ion-item>

          <ion-item>
            <ion-icon slot="start" :icon="callOutline"></ion-icon>
            <ion-label>
              <h3>Phone</h3>
              <p>{{ user?.phone || 'Not provided' }}</p>
            </ion-label>
          </ion-item>
        </ion-list>

        <ion-list>
          <ion-list-header>
            <ion-label>Actions</ion-label>
          </ion-list-header>

          <ion-item button @click="handleLogout">
            <ion-icon slot="start" :icon="logOutOutline" color="danger"></ion-icon>
            <ion-label color="danger">Logout</ion-label>
          </ion-item>
        </ion-list>

        <div class="app-info">
          <p>TappyPass v1.0.0</p>
          <p>Bus Booking System</p>
        </div>
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
  IonList,
  IonListHeader,
  IonItem,
  IonLabel,
  IonIcon,
  alertController,
} from '@ionic/vue';
import {
  personCircleOutline,
  personOutline,
  mailOutline,
  callOutline,
  logOutOutline,
} from 'ionicons/icons';
import { authService } from '@/services/auth';

const router = useRouter();
const user = ref(authService.getUser());

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Logout',
        role: 'confirm',
        handler: async () => {
          await authService.logout();
          router.push('/login');
        },
      },
    ],
  });

  await alert.present();
};
</script>

<style scoped>
.profile-header {
  text-align: center;
  padding: 32px 0;
}

.avatar {
  width: 100px;
  height: 100px;
  margin: 0 auto 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--ion-color-light);
  border-radius: 50%;
}

.avatar ion-icon {
  font-size: 80px;
  color: var(--ion-color-primary);
}

.profile-header h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
}

.profile-header p {
  margin: 0;
  color: var(--ion-color-medium);
}

.app-info {
  text-align: center;
  padding: 32px 0;
  color: var(--ion-color-medium);
  font-size: 14px;
}

.app-info p {
  margin: 4px 0;
}
</style>
