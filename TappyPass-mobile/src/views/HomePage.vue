<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>TappyPass</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Home</ion-title>
        </ion-toolbar>
      </ion-header>

      <div class="ion-padding">
        <div class="welcome-card">
          <ion-icon :icon="busOutline" class="welcome-icon"></ion-icon>
          <h2>Welcome, {{ user?.name }}!</h2>
          <p>Book your bus tickets easily</p>
        </div>

        <ion-button expand="block" size="large" @click="goToNewBooking" class="book-button">
          <ion-icon slot="start" :icon="addCircleOutline"></ion-icon>
          New Booking
        </ion-button>

        <div class="quick-stats">
          <h3>Quick Stats</h3>
          <ion-grid>
            <ion-row>
              <ion-col size="6">
                <div class="stat-card">
                  <ion-icon :icon="ticketOutline" class="stat-icon"></ion-icon>
                  <div class="stat-value">{{ stats.total }}</div>
                  <div class="stat-label">Total Bookings</div>
                </div>
              </ion-col>
              <ion-col size="6">
                <div class="stat-card">
                  <ion-icon :icon="checkmarkCircleOutline" class="stat-icon success"></ion-icon>
                  <div class="stat-value">{{ stats.confirmed }}</div>
                  <div class="stat-label">Confirmed</div>
                </div>
              </ion-col>
            </ion-row>
            <ion-row>
              <ion-col size="6">
                <div class="stat-card">
                  <ion-icon :icon="timeOutline" class="stat-icon warning"></ion-icon>
                  <div class="stat-value">{{ stats.pending }}</div>
                  <div class="stat-label">Pending</div>
                </div>
              </ion-col>
              <ion-col size="6">
                <div class="stat-card">
                  <ion-icon :icon="closeCircleOutline" class="stat-icon danger"></ion-icon>
                  <div class="stat-value">{{ stats.cancelled }}</div>
                  <div class="stat-label">Cancelled</div>
                </div>
              </ion-col>
            </ion-row>
          </ion-grid>
        </div>

        <div class="recent-bookings">
          <h3>Recent Bookings</h3>
          <ion-list v-if="recentBookings.length > 0">
            <ion-item
              v-for="booking in recentBookings"
              :key="booking.id"
              button
              @click="viewBooking(booking.id)"
            >
              <ion-icon slot="start" :icon="ticketOutline"></ion-icon>
              <ion-label>
                <h2>{{ booking.from_location }} → {{ booking.to_location }}</h2>
                <p>{{ formatDate(booking.travel_date) }}</p>
              </ion-label>
              <ion-badge :color="getStatusColor(booking.status)" slot="end">
                {{ booking.status }}
              </ion-badge>
            </ion-item>
          </ion-list>
          <div v-else class="empty-state">
            <ion-icon :icon="ticketOutline" class="empty-icon"></ion-icon>
            <p>No bookings yet</p>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButton,
  IonIcon,
  IonList,
  IonItem,
  IonLabel,
  IonBadge,
  IonGrid,
  IonRow,
  IonCol,
} from '@ionic/vue';
import {
  busOutline,
  addCircleOutline,
  ticketOutline,
  checkmarkCircleOutline,
  timeOutline,
  closeCircleOutline,
} from 'ionicons/icons';
import { authService } from '@/services/auth';
import { bookingService, Booking } from '@/services/booking';

const router = useRouter();
const user = ref(authService.getUser());
const bookings = ref<Booking[]>([]);

const stats = computed(() => ({
  total: bookings.value.length,
  confirmed: bookings.value.filter(b => b.status === 'confirmed').length,
  pending: bookings.value.filter(b => b.status === 'pending').length,
  cancelled: bookings.value.filter(b => b.status === 'cancelled').length,
}));

const recentBookings = computed(() => bookings.value.slice(0, 5));

const loadBookings = async () => {
  try {
    bookings.value = await bookingService.getBookings();
  } catch (error) {
    console.error('Failed to load bookings:', error);
  }
};

const goToNewBooking = () => {
  router.push('/new-booking');
};

const viewBooking = (id: number) => {
  router.push(`/booking/${id}`);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'confirmed':
      return 'success';
    case 'pending':
      return 'warning';
    case 'cancelled':
      return 'danger';
    default:
      return 'medium';
  }
};

onMounted(() => {
  loadBookings();
});
</script>

<style scoped>
.welcome-card {
  background: linear-gradient(135deg, var(--ion-color-primary) 0%, var(--ion-color-secondary) 100%);
  color: white;
  padding: 32px;
  border-radius: 16px;
  text-align: center;
  margin-bottom: 24px;
}

.welcome-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.welcome-card h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
}

.welcome-card p {
  margin: 0;
  opacity: 0.9;
}

.book-button {
  margin-bottom: 32px;
  height: 56px;
  font-weight: 600;
}

.quick-stats {
  margin-bottom: 32px;
}

.quick-stats h3 {
  margin: 0 0 16px 0;
  font-size: 20px;
  font-weight: 600;
}

.stat-card {
  background: var(--ion-color-light);
  padding: 16px;
  border-radius: 12px;
  text-align: center;
}

.stat-icon {
  font-size: 32px;
  color: var(--ion-color-primary);
  margin-bottom: 8px;
}

.stat-icon.success {
  color: var(--ion-color-success);
}

.stat-icon.warning {
  color: var(--ion-color-warning);
}

.stat-icon.danger {
  color: var(--ion-color-danger);
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  color: var(--ion-color-medium);
}

.recent-bookings h3 {
  margin: 0 0 16px 0;
  font-size: 20px;
  font-weight: 600;
}

.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: var(--ion-color-medium);
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
}
</style>
