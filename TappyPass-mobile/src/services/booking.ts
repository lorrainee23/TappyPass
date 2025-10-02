import api from './api';

export interface Booking {
  id: number;
  booking_number: string;
  passenger_name: string;
  phone: string;
  from_location: string;
  to_location: string;
  travel_date: string;
  travel_time: string;
  seats: number;
  amount: number;
  status: 'pending' | 'confirmed' | 'cancelled';
  qr_code?: string;
  transaction?: Transaction;
  created_at: string;
}

export interface Transaction {
  id: number;
  transaction_number: string;
  amount: number;
  payment_method: string;
  receipt_image?: string;
  payment_status: 'pending' | 'paid' | 'rejected';
  admin_notes?: string;
}

export interface NewBookingData {
  route_id: number;
  passenger_name: string;
  phone: string;
  travel_date: string;
  seats: number;
}

class BookingService {
  async getBookings(): Promise<Booking[]> {
    const response = await api.get('/bookings');
    return response.data;
  }

  async getBooking(id: number): Promise<Booking> {
    const response = await api.get(`/bookings/${id}`);
    return response.data;
  }

  async createBooking(data: NewBookingData) {
    const response = await api.post('/bookings', data);
    return response.data;
  }

  async uploadReceipt(bookingId: number, file: File) {
    const formData = new FormData();
    formData.append('receipt', file);

    const response = await api.post(`/bookings/${bookingId}/upload-receipt`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  async cancelBooking(bookingId: number) {
    const response = await api.post(`/bookings/${bookingId}/cancel`);
    return response.data;
  }

  async getGcashQr(): Promise<string | null> {
    const response = await api.get('/settings/gcash-qr');
    return response.data.qr_code;
  }
}

export const bookingService = new BookingService();
