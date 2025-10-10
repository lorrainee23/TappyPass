<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
        </ion-buttons>
        <ion-title>Booking Details</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="ion-padding">
      <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>
      
      <div v-if="loading" class="loading-container">
        <ion-spinner name="crescent"></ion-spinner>
      </div>

      <div v-else-if="booking" class="booking-detail">
        <div class="status-banner" :class="booking.status">
          <ion-icon :icon="getStatusIcon(booking.status)" class="status-icon"></ion-icon>
          <div>
            <h3>{{ booking.status.toUpperCase() }}</h3>
            <p>{{ getStatusMessage(booking.status) }}</p>
          </div>
        </div>

        <ion-card>
          <ion-card-header>
            <ion-card-subtitle>Booking Number</ion-card-subtitle>
            <ion-card-title>{{ booking.booking_number }}</ion-card-title>
          </ion-card-header>

          <ion-card-content>
            <div class="detail-section">
              <h4>Passenger Information</h4>
              <div class="detail-row">
                <span class="label">Name:</span>
                <span class="value">{{ booking.passenger_name }}</span>
              </div>
              <div class="detail-row">
                <span class="label">Phone:</span>
                <span class="value">{{ booking.phone }}</span>
              </div>
            </div>

            <div class="detail-section">
              <h4>Travel Information</h4>
              <div class="route-display">
                <div class="location-point">
                  <ion-icon :icon="locationOutline"></ion-icon>
                  <span>{{ booking.from_location }}</span>
                </div>
                <div class="route-line"></div>
                <div class="location-point">
                  <ion-icon :icon="locationOutline"></ion-icon>
                  <span>{{ booking.to_location }}</span>
                </div>
              </div>
              <div class="detail-row">
                <span class="label">Date:</span>
                <span class="value">{{ formatDate(booking.travel_date) }}</span>
              </div>
              <div class="detail-row">
                <span class="label">Time:</span>
                <span class="value">{{ booking.travel_time }}</span>
              </div>
              <div class="detail-row">
                <span class="label">Seats:</span>
                <span class="value">{{ booking.seats }}</span>
              </div>
            </div>

            <div class="detail-section">
              <h4>Payment Information</h4>
              <div class="detail-row">
                <span class="label">Amount:</span>
                <span class="value amount">₱{{ Number(booking.amount).toFixed(2) }}</span>
              </div>
              <div v-if="booking.transaction" class="detail-row">
                <span class="label">Payment Status:</span>
                <ion-badge :color="getPaymentStatusColor(booking.transaction.payment_status)">
                  {{ booking.transaction.payment_status }}
                </ion-badge>
              </div>
              <div v-if="booking.transaction?.admin_notes" class="admin-notes">
                <p class="label">Admin Notes:</p>
                <p class="notes-text">{{ booking.transaction.admin_notes }}</p>
              </div>
            </div>
          </ion-card-content>
        </ion-card>

        <!-- QR Code for confirmed bookings -->
        <ion-card v-if="booking.status === 'confirmed' && booking.qr_code">
          <ion-card-header>
            <ion-card-title>Boarding Pass</ion-card-title>
            <ion-card-subtitle>Show this QR code when boarding</ion-card-subtitle>
          </ion-card-header>
          <ion-card-content class="qr-section">
            <img :src="getQrCodeUrl(booking.qr_code)" alt="Booking QR Code" class="qr-code" />
          </ion-card-content>
        </ion-card>

        <!-- Receipt Image -->
        <ion-card v-if="booking.transaction?.receipt_image">
          <ion-card-header>
            <ion-card-title>Payment Receipt</ion-card-title>
            <ion-card-subtitle v-if="booking.transaction.payment_status === 'rejected'">
              Payment was rejected. Please upload a new receipt.
            </ion-card-subtitle>
          </ion-card-header>
          <ion-card-content>
            <img :src="getReceiptUrl(booking.transaction.receipt_image)" alt="Receipt" class="receipt-image" />
            
            <!-- Re-upload button for rejected payments -->
            <div v-if="booking.transaction.payment_status === 'rejected' && booking.status !== 'cancelled'">
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                @change="handleFileSelect"
                style="display: none"
              />
              <ion-button expand="block" @click="triggerFileInput" :disabled="uploading" class="reupload-btn">
                <ion-spinner v-if="uploading" name="crescent"></ion-spinner>
                <span v-else>
                  <ion-icon :icon="cloudUploadOutline" slot="start"></ion-icon>
                  Upload New Receipt
                </span>
              </ion-button>
              <p class="rejection-info" v-if="booking.transaction.rejection_count">
                Attempt {{ booking.transaction.rejection_count }} of 3
              </p>
            </div>
          </ion-card-content>
        </ion-card>

        <!-- Actions -->
        <div v-if="booking.status === 'pending'" class="actions">
          <ion-button expand="block" color="danger" @click="confirmCancel">
            Cancel Booking
          </ion-button>
        </div>

        <div class="booking-date">
          <p>Booked on {{ formatDateTime(booking.created_at) }}</p>
        </div>
      </div>

      <div v-else class="error-state">
        <ion-icon :icon="alertCircleOutline" class="error-icon"></ion-icon>
        <p>Booking not found</p>
        <ion-button @click="goBack">Go Back</ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardSubtitle,
  IonCardContent,
  IonBadge,
  IonIcon,
  IonButton,
  IonSpinner,
  IonRefresher,
  IonRefresherContent,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  locationOutline,
  checkmarkCircleOutline,
  timeOutline,
  closeCircleOutline,
  alertCircleOutline,
  cloudUploadOutline,
} from 'ionicons/icons';
import { bookingService, Booking } from '@/services/booking';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const uploading = ref(false);
const booking = ref<Booking | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const loadBooking = async () => {
  try {
    const id = parseInt(route.params.id as string);
    booking.value = await bookingService.getBooking(id);
  } catch (error) {
    console.error('Failed to load booking:', error);
  } finally {
    loading.value = false;
  }
};

const handleRefresh = async (event: any) => {
  await loadBooking();
  event.target.complete();
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusIcon = (status: string) => {
  switch (status) {
    case 'confirmed':
      return checkmarkCircleOutline;
    case 'pending':
      return timeOutline;
    case 'cancelled':
      return closeCircleOutline;
    default:
      return alertCircleOutline;
  }
};

const getStatusMessage = (status: string) => {
  switch (status) {
    case 'confirmed':
      return 'Your booking is confirmed. Show the QR code when boarding.';
    case 'pending':
      return 'Waiting for payment verification by admin.';
    case 'cancelled':
      return 'This booking has been cancelled.';
    default:
      return '';
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

const getQrCodeUrl = (path: string) => {
  return `http://localhost:8000/storage/${path}`;
};

const getReceiptUrl = (path: string) => {
  return `http://localhost:8000/storage/${path}`;
};

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileSelect = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  
  if (!file) return;
  
  // Validate file type
  if (!file.type.startsWith('image/')) {
    const toast = await toastController.create({
      message: 'Please select an image file',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
    return;
  }
  
  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    const toast = await toastController.create({
      message: 'Image size must be less than 2MB',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
    return;
  }
  
  await uploadReceipt(file);
};

const uploadReceipt = async (file: File) => {
  uploading.value = true;
  try {
    const id = parseInt(route.params.id as string);
    await bookingService.uploadReceipt(id, file);
    
    const toast = await toastController.create({
      message: 'Receipt uploaded successfully! Waiting for admin verification.',
      duration: 3000,
      color: 'success',
      position: 'top',
    });
    await toast.present();
    
    // Reload booking to show updated receipt
    await loadBooking();
  } catch (error: any) {
    const toast = await toastController.create({
      message: error.response?.data?.message || 'Failed to upload receipt',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    uploading.value = false;
    // Reset file input
    if (fileInput.value) {
      fileInput.value.value = '';
    }
  }
};

const confirmCancel = async () => {
  const alert = await alertController.create({
    header: 'Cancel Booking',
    message: 'Are you sure you want to cancel this booking?',
    buttons: [
      {
        text: 'No',
        role: 'cancel',
      },
      {
        text: 'Yes, Cancel',
        role: 'confirm',
        handler: async () => {
          await cancelBooking();
        },
      },
    ],
  });

  await alert.present();
};

const cancelBooking = async () => {
  if (!booking.value) return;

  try {
    await bookingService.cancelBooking(booking.value.id);
    
    const toast = await toastController.create({
      message: 'Booking cancelled successfully',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();

    await loadBooking();
  } catch (error: any) {
    const toast = await toastController.create({
      message: error.response?.data?.message || 'Failed to cancel booking',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  }
};

const goBack = () => {
  router.push('/tabs/bookings');
};

onMounted(() => {
  loadBooking();
});
</script>

<style scoped>
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 200px;
}

.status-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 16px;
}

.status-banner.confirmed {
  background: var(--ion-color-success-tint);
  color: var(--ion-color-success-contrast);
}

.status-banner.pending {
  background: var(--ion-color-warning-tint);
  color: var(--ion-color-warning-contrast);
}

.status-banner.cancelled {
  background: var(--ion-color-danger-tint);
  color: var(--ion-color-danger-contrast);
}

.status-icon {
  font-size: 48px;
}

.status-banner h3 {
  margin: 0 0 4px 0;
  font-size: 18px;
  font-weight: bold;
}

.status-banner p {
  margin: 0;
  font-size: 14px;
  opacity: 0.9;
}

.detail-section {
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--ion-color-light);
}

.detail-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.detail-section h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: var(--ion-color-medium);
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.label {
  color: var(--ion-color-medium);
  font-size: 14px;
}

.value {
  font-weight: 600;
  font-size: 14px;
}

.value.amount {
  color: var(--ion-color-primary);
  font-size: 18px;
}

.route-display {
  background: var(--ion-color-light);
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 16px;
}

.location-point {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  margin-bottom: 8px;
}

.route-line {
  width: 2px;
  height: 24px;
  background: var(--ion-color-medium);
  margin-left: 12px;
  margin-bottom: 8px;
}

.admin-notes {
  background: var(--ion-color-light);
  padding: 12px;
  border-radius: 8px;
  margin-top: 12px;
}

.notes-text {
  margin: 8px 0 0 0;
  font-size: 14px;
}

.qr-section {
  text-align: center;
  padding: 24px;
}

.qr-code {
  max-width: 300px;
  width: 100%;
  height: auto;
}
.receipt-image {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid var(--ion-color-light);
  color: var(--ion-color-medium);
  font-size: 14px;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 64px 24px;
}

.error-state {
  text-align: center;
  padding: 64px 24px;
  color: var(--ion-color-medium);
}

.error-icon {
  font-size: 80px;
  color: var(--ion-color-danger);
  margin-bottom: 16px;
}

.reupload-btn {
  margin-top: 16px;
}

.rejection-info {
  text-align: center;
  margin-top: 8px;
  color: var(--ion-color-warning);
  font-size: 14px;
  font-weight: 500;
}
</style>
