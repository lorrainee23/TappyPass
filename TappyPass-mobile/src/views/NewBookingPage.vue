<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/tabs/home"></ion-back-button>
        </ion-buttons>
        <ion-title>New Booking</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="ion-padding">
        <ion-progress-bar :value="progress"></ion-progress-bar>

        <!-- Step 1: Booking Details -->
        <div v-if="currentStep === 1" class="step-container">
          <h2>Booking Details</h2>
          <form @submit.prevent="nextStep">
            <ion-item lines="full" class="input-item">
              <ion-input
                v-model="formData.passenger_name"
                label="Passenger Name *"
                label-placement="floating"
                required
              ></ion-input>
            </ion-item>

            <ion-item lines="full" class="input-item">
              <ion-input
                v-model="formData.phone"
                type="tel"
                label="Phone Number *"
                label-placement="floating"
                required
              ></ion-input>
            </ion-item>

            <div class="mb-4">
              <label class="block text-sm font-medium mb-2">Select Route *</label>
              <div v-if="loadingRoutes" class="text-center py-4">
                <ion-spinner name="crescent"></ion-spinner>
                <p class="text-gray-500">Loading routes...</p>
              </div>
              <ion-list v-else-if="routes.length > 0">
                <ion-radio-group v-model="selectedRouteId">
                  <ion-item v-for="route in routes" :key="route.id" lines="full">
                    <ion-radio :value="route.id" slot="start"></ion-radio>
                    <ion-label>
                      <h3>{{ route.from_location }} → {{ route.to_location }}</h3>
                      <p>Departure: {{ formatTime(route.departure_time) }}</p>
                      <p class="text-primary font-bold">₱{{ Number(route.price).toFixed(2) }} per seat</p>
                    </ion-label>
                  </ion-item>
                </ion-radio-group>
              </ion-list>
              <div v-else class="text-center py-4 text-gray-500">
                <ion-icon :icon="alertCircleOutline" style="font-size: 48px; margin-bottom: 8px;"></ion-icon>
                <p>No routes available. Please ask admin to create routes.</p>
              </div>
            </div>

            <ion-item lines="full" class="input-item">
              <ion-input
                v-model="formData.travel_date"
                type="date"
                label="Travel Date *"
                label-placement="floating"
                required
              ></ion-input>
            </ion-item>

            <ion-item lines="full" class="input-item">
              <ion-input
                v-model.number="formData.seats"
                type="number"
                min="1"
                max="10"
                label="Number of Seats *"
                label-placement="floating"
                required
              ></ion-input>
            </ion-item>

            <div v-if="selectedRouteId && formData.seats" class="amount-preview">
              <p>Total Amount: <strong>₱{{ calculateTotal().toFixed(2) }}</strong></p>
            </div>

            <ion-button expand="block" type="submit" class="next-button">
              Next: Payment
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </form>
        </div>

        <!-- Step 2: Payment -->
        <div v-if="currentStep === 2" class="step-container">
          <h2>Payment via GCash</h2>

          <div v-if="gcashQrCode" class="gcash-section">
            <p class="instruction">Scan this QR code with your GCash app to pay</p>
            <div class="qr-container">
              <img :src="gcashQrCode" alt="GCash QR Code" class="gcash-qr" />
            </div>
            <div class="amount-display">
              <p>Amount to Pay:</p>
              <h3>₱{{ calculateTotal().toFixed(2) }}</h3>
            </div>
          </div>

          <div v-else class="no-qr">
            <ion-icon :icon="alertCircleOutline" class="alert-icon"></ion-icon>
            <p>GCash QR code not available. Please contact admin.</p>
          </div>

          <div class="button-group">
            <ion-button expand="block" fill="outline" @click="previousStep">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>
            <ion-button expand="block" @click="nextStep">
              Next: Upload Receipt
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>
        </div>

        <!-- Step 3: Upload Receipt -->
        <div v-if="currentStep === 3" class="step-container">
          <h2>Upload Payment Receipt</h2>

          <p class="instruction">Upload a screenshot of your GCash payment receipt</p>

          <div class="upload-section">
            <input
              type="file"
              ref="fileInput"
              accept="image/*"
              @change="handleFileSelect"
              style="display: none"
            />

            <div v-if="!selectedFile" class="upload-placeholder" @click="triggerFileInput">
              <ion-icon :icon="cloudUploadOutline" class="upload-icon"></ion-icon>
              <p>Tap to select receipt image</p>
            </div>

            <div v-else class="preview-container">
              <img :src="previewUrl" alt="Receipt Preview" class="receipt-preview" />
              <ion-button expand="block" fill="outline" @click="triggerFileInput">
                Change Image
              </ion-button>
            </div>
          </div>

          <div class="button-group">
            <ion-button expand="block" fill="outline" @click="previousStep">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>
            <ion-button
              expand="block"
              @click="submitBooking"
              :disabled="!selectedFile || loading"
            >
              <ion-spinner v-if="loading" name="crescent"></ion-spinner>
              <span v-else>Submit Booking</span>
            </ion-button>
          </div>
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
  IonButtons,
  IonBackButton,
  IonItem,
  IonLabel,
  IonInput,
  IonButton,
  IonIcon,
  IonProgressBar,
  IonSpinner,
  IonList,
  IonRadioGroup,
  IonRadio,
  toastController,
} from '@ionic/vue';
import {
  arrowForwardOutline,
  arrowBackOutline,
  cloudUploadOutline,
  alertCircleOutline,
} from 'ionicons/icons';
import { bookingService } from '@/services/booking';
import { authService } from '@/services/auth';
import api from '@/services/api';

const router = useRouter();
const currentStep = ref(1);
const loading = ref(false);
const loadingRoutes = ref(true);
const gcashQrCode = ref<string | null>(null);
const selectedFile = ref<File | null>(null);
const previewUrl = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const createdBookingId = ref<number | null>(null);
const routes = ref<any[]>([]);
const selectedRouteId = ref<number | null>(null);

const user = authService.getUser();

const formData = ref({
  passenger_name: user?.name || '',
  phone: user?.phone || '',
  travel_date: '',
  seats: 1,
});

const progress = computed(() => currentStep.value / 3);

const selectedRoute = computed(() => {
  return routes.value.find(r => r.id === selectedRouteId.value);
});

const calculateTotal = () => {
  if (selectedRoute.value && formData.value.seats) {
    return Number(selectedRoute.value.price) * formData.value.seats;
  }
  return 0;
};

const formatTime = (time: string) => {
  const [hours, minutes] = time.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const displayHour = hour % 12 || 12;
  return `${displayHour}:${minutes} ${ampm}`;
};

const loadRoutes = async () => {
  loadingRoutes.value = true;
  try {
    const response = await api.get('/routes');
    routes.value = response.data;
    console.log('Routes loaded:', routes.value);
  } catch (error: any) {
    console.error('Failed to load routes:', error);
    console.error('Error details:', error.response?.data);
    const toast = await toastController.create({
      message: 'Failed to load routes: ' + (error.response?.data?.message || error.message),
      duration: 5000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    loadingRoutes.value = false;
  }
};

const loadGcashQr = async () => {
  try {
    gcashQrCode.value = await bookingService.getGcashQr();
  } catch (error) {
    console.error('Failed to load GCash QR:', error);
  }
};

const nextStep = async () => {
  if (currentStep.value === 1) {
    if (!selectedRouteId.value) {
      const toast = await toastController.create({
        message: 'Please select a route',
        duration: 3000,
        color: 'warning',
        position: 'top',
      });
      await toast.present();
      return;
    }
    
    // Create booking
    try {
      loading.value = true;
      const bookingData = {
        route_id: selectedRouteId.value,
        passenger_name: formData.value.passenger_name,
        phone: formData.value.phone,
        travel_date: formData.value.travel_date,
        seats: formData.value.seats,
      };
      const response = await bookingService.createBooking(bookingData);
      createdBookingId.value = response.booking.id;
      currentStep.value++;
    } catch (error: any) {
      const toast = await toastController.create({
        message: error.response?.data?.message || 'Failed to create booking',
        duration: 3000,
        color: 'danger',
        position: 'top',
      });
      await toast.present();
    } finally {
      loading.value = false;
    }
  } else {
    currentStep.value++;
  }
};

const previousStep = () => {
  currentStep.value--;
};

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  
  if (file) {
    selectedFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
  }
};

const submitBooking = async () => {
  if (!selectedFile.value || !createdBookingId.value) return;

  loading.value = true;
  try {
    await bookingService.uploadReceipt(createdBookingId.value, selectedFile.value);
    
    const toast = await toastController.create({
      message: 'Booking submitted successfully! Waiting for admin confirmation.',
      duration: 3000,
      color: 'success',
      position: 'top',
    });
    await toast.present();

    router.push(`/booking/${createdBookingId.value}`);
  } catch (error: any) {
    const toast = await toastController.create({
      message: error.response?.data?.message || 'Failed to upload receipt',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadRoutes();
  loadGcashQr();
});
</script>

<style scoped>
.step-container {
  max-width: 600px;
  margin: 0 auto;
  padding-top: 24px;
}

.step-container h2 {
  margin: 0 0 24px 0;
  font-size: 24px;
  font-weight: 600;
}

.input-item {
  margin-bottom: 16px;
  --background: var(--ion-color-light);
  border-radius: 8px;
}

.next-button {
  margin-top: 24px;
  height: 48px;
  font-weight: 600;
}

.gcash-section {
  text-align: center;
  margin-bottom: 32px;
}

.instruction {
  color: var(--ion-color-medium);
  margin-bottom: 24px;
}

.qr-container {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 24px;
  display: inline-block;
}

.gcash-qr {
  max-width: 300px;
  width: 100%;
  height: auto;
}

.amount-display {
  background: var(--ion-color-light);
  padding: 16px;
  border-radius: 12px;
}

.amount-display p {
  margin: 0 0 8px 0;
  color: var(--ion-color-medium);
}

.amount-display h3 {
  margin: 0;
  font-size: 32px;
  color: var(--ion-color-primary);
  font-weight: bold;
}

.no-qr {
  text-align: center;
  padding: 48px 24px;
  color: var(--ion-color-medium);
}

.alert-icon {
  font-size: 64px;
  color: var(--ion-color-warning);
  margin-bottom: 16px;
}

.button-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 24px;
}

.upload-section {
  margin-bottom: 32px;
}

.upload-placeholder {
  border: 2px dashed var(--ion-color-medium);
  border-radius: 12px;
  padding: 48px 24px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
}

.upload-placeholder:hover {
  border-color: var(--ion-color-primary);
  background: var(--ion-color-light);
}

.upload-icon {
  font-size: 64px;
  color: var(--ion-color-medium);
  margin-bottom: 16px;
}

.preview-container {
  text-align: center;
}

.receipt-preview {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.amount-preview {
  background: var(--ion-color-light);
  padding: 16px;
  border-radius: 12px;
  margin: 16px 0;
  text-align: center;
  font-size: 18px;
}

.amount-preview strong {
  color: var(--ion-color-primary);
  font-size: 24px;
}

.mb-4 {
  margin-bottom: 16px;
}

.text-primary {
  color: var(--ion-color-primary);
}

.font-bold {
  font-weight: bold;
}

.block {
  display: block;
}

.text-sm {
  font-size: 14px;
}

.font-medium {
  font-weight: 500;
}

.text-center {
  text-align: center;
}

.py-4 {
  padding-top: 16px;
  padding-bottom: 16px;
}

.text-gray-500 {
  color: var(--ion-color-medium);
}
</style>
