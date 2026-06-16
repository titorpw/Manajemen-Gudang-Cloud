import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID
};

let auth = null;

const isConfigured = !!(firebaseConfig.apiKey && firebaseConfig.projectId && firebaseConfig.appId);

if (isConfigured) {
    try {
        const app = initializeApp(firebaseConfig);
        auth = getAuth(app);
    } catch (error) {
        console.error('Gagal menginisialisasi Firebase SDK:', error);
    }
} else {
    console.warn('Firebase Client belum dikonfigurasi di file .env. Silakan atur variabel VITE_FIREBASE_*');
}

export { auth, isConfigured };
