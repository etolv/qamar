if ('function' === typeof importScripts) {
  importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
  importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

  const firebaseConfig = {
    apiKey: 'AIzaSyA_d_q527ABfel4cFbvRkVto0gQGnQg8hE',
    authDomain: 'qamar-samaya.firebaseapp.com',
    projectId: 'qamar-samaya',
    storageBucket: 'qamar-samaya.appspot.com',
    messagingSenderId: '671187155981',
    appId: '1:671187155981:web:f35b6e7fe7a04c6e872944',
    measurementId: 'G-VHXM0JCS45'
  };

  // Initialize Firebase
  const app = firebase.initializeApp(firebaseConfig);
  const messaging = firebase.messaging();

  messaging.onBackgroundMessage(payload => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const title = payload.data.title;
    const options = {
      body: payload.data.body,
      icon: "{{ asset('assets/img/qamar_samaya_logo.svg') }}",
      requireInteraction: true
    };

    self.registration.showNotification(title, options);
  });
}
