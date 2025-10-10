<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Bookings</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>
      
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Bookings</ion-title>
        </ion-toolbar>
      </ion-header>

      <div class="ion-padding">
        <ion-segment v-model="selectedSegment" @ionChange="filterBookings">
          <ion-segment-button value="all">
            <ion-label>All</ion-label>
          </ion-segment-button>
          <ion-segment-button value="pending">
            <ion-label>Pending</ion-label>
          </ion-segment-button>
          <ion-segment-button value="confirmed">
            <ion-label>Confirmed</ion-label>
          </ion-segment-button>
        </ion-segment>

        <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
          <ion-refresher-content></ion-refresher-content>
        </ion-refresher>

        <ion-list v-if="filteredBookings.length > 0" class="booking-list">
          <ion-card
            v-for="booking in filteredBookings"
            :key="booking.id"
            button
            @click="viewBooking(booking.id)"
            class="booking-card"
          >
            <ion-card-header>
              <div class="card-header">
                <ion-card-subtitle>{{ booking.booking_number }}</ion-card-subtitle>
                <ion-badge :color="getStatusColor(booking.status)">
                  {{ booking.status }}
                </ion-badge>
              </div>
            </ion-card-header>

            <ion-card-content>
              <div class="route-info">
                <div class="location">
                  <ion-icon :icon="locationOutline"></ion-icon>
                  <span>{{ booking.from_location }}</span>
                </div>
                <ion-icon :icon="arrowForwardOutline" class="arrow"></ion-icon>
                <div class="location">
                  <ion-icon :icon="locationOutline"></ion-icon>
                  <span>{{ booking.to_location }}</span>
                </div>
              </div>

              <div class="booking-details">
                <div class="detail-item">
                  <ion-icon :icon="calendarOutline"></ion-icon>
                  <span>{{ formatDate(booking.travel_date) }}</span>
                </div>
                <div class="detail-item">
                  <ion-icon :icon="timeOutline"></ion-icon>
                  <span>{{ booking.travel_time }}</span>
                </div>
                <div class="detail-item">
                  <ion-icon :icon="peopleOutline"></ion-icon>
                  <span>{{ booking.seats }} seat(s)</span>
                </div>
              </div>

              <div class="amount">
                <strong>₱{{ Number(booking.amount).toFixed(2) }}</strong>
              </div>

              <div v-if="booking.transaction" class="payment-status">
                <ion-badge :color="getPaymentStatusColor(booking.transaction.payment_status)">
                  Payment: {{ booking.transaction.payment_status }}
                </ion-badge>
              </div>
            </ion-card-content>
          </ion-card>
        </ion-list>

        <div v-else class="empty-state">
          <ion-icon :icon="ticketOutline" class="empty-icon"></ion-icon>
          <p>No {{ selectedSegment !== 'all' ? selectedSegment : '' }} bookings found</p>
          <ion-button @click="goToNewBooking">Create New Booking</ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonSegment,
  IonSegmentButton,
  IonLabel,
  IonList,
  IonCard,
  IonCardHeader,
  IonCardSubtitle,
  IonCardContent,
  IonBadge,
  IonIcon,
  IonButton,
  IonRefresher,
  IonRefresherContent,
} from '@ionic/vue';
import {
  ticketOutline,
  locationOutline,
  arrowForwardOutline,
  calendarOutline,
  timeOutline,
  peopleOutline,
} from 'ionicons/icons';
import { bookingService, Booking } from '@/services/booking';

const router = useRouter();
const selectedSegment = ref('all');
const bookings = ref<Booking[]>([]);

const filteredBookings = computed(() => {
  if (selectedSegment.value === 'all') {
    return bookings.value;
  }
  return bookings.value.filter(b => b.status === selectedSegment.value);
});

const loadBookings = async () => {
  try {
    bookings.value = await bookingService.getBookings();
  } catch (error) {
    console.error('Failed to load bookings:', error);
  }
};
const handleRefresh = async (event: any) => {
  await loadBookings();
  event.target.complete();
};

const filterBookings = () => {
  // Filter is handled by computed property
};

const viewBooking = (id: number) => {
  router.push(`/tabs/booking/${id}`);
};

const goToNewBooking = () => {
  router.push('/tabs/new-booking');
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

const getPaymentStatusColor = (status: string) => {
  switch (status) {
    case 'paid':
      return 'success';
    case 'pending':
      return 'warning';
    case 'rejected':
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
.booking-list {
  margin-top: 16px;
}

.booking-card {
  margin-bottom: 16px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.route-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
  padding: 12px;
  background: var(--ion-color-light);
  border-radius: 8px;
}

.location {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
}

.arrow {
  color: var(--ion-color-medium);
}

.booking-details {
  display: flex;
  gap: 16px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--ion-color-medium);
  font-size: 14px;
}

.amount {
  font-size: 20px;
  color: var(--ion-color-primary);
  margin-bottom: 8px;
}

.payment-status {
  margin-top: 8px;
}

.empty-state {
  text-align: center;
  padding: 64px 24px;
  color: var(--ion-color-medium);
}

.empty-icon {
  font-size: 80px;
  margin-bottom: 16px;
}
</style>
