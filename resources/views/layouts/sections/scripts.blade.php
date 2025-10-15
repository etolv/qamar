<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js', 'resources/assets/vendor/libs/node-waves/node-waves.js', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/libs/hammer/hammer.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/js/menu.js'])
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->


@vite(['resources/assets/js/main.js', 'resources/assets/js/myscript.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
{{-- pdf js --}}
<script>
    // import pdfMake from 'pdfmake/build/pdfmake';
    // import pdfFonts from 'pdfmake/build/vfs_fonts';

    // // Add custom fonts to the virtual file system
    // pdfMake.vfs['Roboto-Regular.ttf'] = require('./assets/fonts/Roboto-Regular.ttf');
    // pdfMake.vfs['Roboto-Bold.ttf'] = require('./assets/fonts/Roboto-Bold.ttf');
    // pdfMake.vfs['Roboto-Italic.ttf'] = require('./assets/fonts/Roboto-Italic.ttf');
    // pdfMake.vfs['Roboto-BoldItalic.ttf'] = require('./assets/fonts/Roboto-BoldItalic.ttf');

    // // Define the font configuration
    // pdfMake.fonts = {
    //     Roboto: {
    //         normal: 'Roboto-Regular.ttf',
    //         bold: 'Roboto-Bold.ttf',
    //         italics: 'Roboto-Italic.ttf',
    //         bolditalics: 'Roboto-BoldItalic.ttf',
    //     }
    // };

    // // Initialize DataTables
    // $('#example').DataTable({
    //     dom: 'Bfrtip',
    //     buttons: [{
    //         extend: 'pdfHtml5',
    //         text: 'PDF',
    //         customize: function(doc) {
    //             doc.defaultStyle = {
    //                 font: 'Roboto',
    //                 fontSize: 10,
    //             };
    //         }
    //     }]
    // });
</script>
{{-- pdf js --}}
<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/9.6.5/firebase-app.js";
    import {
        getAnalytics
    } from "https://www.gstatic.com/firebasejs/9.6.5/firebase-analytics.js";
    import {
        getMessaging,
        getToken,
        onMessage
    } from "https://www.gstatic.com/firebasejs/9.6.5/firebase-messaging.js";
    // Initialize Firebase
    const firebaseConfig = {
        apiKey: 'AIzaSyA_d_q527ABfel4cFbvRkVto0gQGnQg8hE',
        authDomain: 'qamar-samaya.firebaseapp.com',
        projectId: 'qamar-samaya',
        storageBucket: 'qamar-samaya.appspot.com',
        messagingSenderId: '671187155981',
        appId: '1:671187155981:web:f35b6e7fe7a04c6e872944',
        measurementId: 'G-VHXM0JCS45'
    };

    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);
    const messaging = getMessaging();
    getToken(messaging, {
        vapidKey: "BFT6QQyKMpEGz5O8hWhGtKQShOEMbajSMTVSlAYW36IxvQeIoT223P9UAsWO_zkZrpLQgHyHXPbCqqnP2dci620"
    }).then(function(response) {
        console.log('token : ');
        console.log(response);
        $.ajax({
            url: route('notification.updateFcmTokenAdmin'),
            type: 'GET',
            data: {
                "_token": "{{ csrf_token() }}",
                notification_token: response
            },
            success: function(_response) {
                console.log("Registered Successfully. Token: " + response);

            },
            error: function(error) {
                console.log(error)
            },
        });
    });
    onMessage(messaging, (payload) => {
        console.log("message firebase");
        console.log(payload);

        var numberAdminNotification = document.getElementById('numberAdminNotification');

        var number = Number(numberAdminNotification.innerHTML);
        numberAdminNotification.innerHTML = String(++number);

        var title = payload.notification.title;
        var body = payload.notification.body;
        $(".dropdown-notifications-list").before(`
         <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="{{ Auth::check() ? $authenticated_user?->getFirstMediaUrl('profile') ?? asset('assets/img/illustrations/NoImage.png') : '' }}" alt class="h-auto rounded-circle">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">${title}</h6>
                        <p class="mb-0">${body}</p>
                        <small class="text-muted">1h ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                      </div>
                    </div>
                  </li>`);


    });
</script>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').show();
        setTimeout(function() {
            $('#loadingSpinner').remove();
        }, 5000);
        $(window).on('load', function() {
            $('#loadingSpinner').fadeOut('slow', function() {
                $(this).remove(); // Remove the spinner from the DOM after fadeOut
            });
        });
    });
    const LOCALE = {!! json_encode(\Illuminate\Support\Facades\Lang::get('locale')) !!};
    const current_system_locale = "{{ app()->getLocale() }}";
</script>
<!-- END: Page JS-->
