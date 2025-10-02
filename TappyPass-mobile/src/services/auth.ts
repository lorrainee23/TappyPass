import api from './api';

export interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
  role: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone?: string;
}

class AuthService {
  async login(credentials: LoginCredentials) {
    const response = await api.post('/login', credentials);
    const { user, token } = response.data;
    
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    
    return { user, token };
  }

  async register(data: RegisterData) {
    const response = await api.post('/register', data);
    const { user, token } = response.data;
    
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    
    return { user, token };
  }

  async logout() {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  }

  isAuthenticated(): boolean {
    return !!localStorage.getItem('token');
  }

  getUser(): User | null {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
  }

  async fetchUser() {
    const response = await api.get('/user');
    const user = response.data;
    localStorage.setItem('user', JSON.stringify(user));
    return user;
  }
}

export const authService = new AuthService();
