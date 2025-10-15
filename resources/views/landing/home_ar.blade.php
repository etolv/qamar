<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Qamar samaya</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha1/css/bootstrap.rtl.min.css"
        integrity="sha512-8HEAeCQ9956CFmdyqmLfgAHRl3QB+NJ/Du/zEj9enTYQAx6YDi9453s5snpCBjckg4biHeVw0HsgiMo2nCiD+w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css" media="all" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/brands.min.css" media="all" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/solid.min.css" media="all" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/regular.min.css"
        integrity="sha512-TzeemgHmrSO404wTLeBd76DmPp5TjWY/f2SyZC6/3LsutDYMVYfOx2uh894kr0j9UM6x39LFHKTeLn99iz378A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/home-style.css" />
    <link rel="stylesheet" href="assets/css/home-style-rtl.css" />
</head>

<body class="ar">
    <header>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">
                                <img src="assets/imgs/logo/logo.svg" alt="" />
                            </a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                                aria-labelledby="offcanvasNavbarLabel">
                                <div class="offcanvas-header">
                                    <img src="assets/imgs/logo/logo.svg" alt="" />
                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <ul class="navbar-nav justify-content-end flex-grow-1">
                                        <li class="nav-item">
                                            <a class="nav-link active" aria-current="page" href="#home">الرئيسية</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#about-us">من نحن</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#download">تحميل التطبيق</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#specs">الفوائد</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#faq">الأسئلة الشائعة</a>
                                        </li>
                                    </ul>

                                    <ul class="navbar-nav justify-content-end flex-grow-1 right-nav">
                                        <li class="nav-item language language-switcher">
                                            <a class="nav-link" href="{{ route('swap', 'en') }}">EN</a>
                                        </li>
                                        <!-- <li class="nav-item language">
                                                <a class="nav-link" href="#download">Get Started</a>
                                            </li> -->
                                    </ul>
                                    <div class="footer-icon d-flex justify-content-center">
                                        <a href="https://www.facebook.com/qamar.samaya.5">
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </a>
                                        <a href="https://www.instagram.com/qamarsamaya">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                        <a href="https://www.youtube.com/@qamarsamaya">
                                            <i class="fa-brands fa-youtube"></i>
                                        </a>
                                        <a href="https://wa.me/966551114415">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>

                                <img src="assets/imgs/logo/logo.svg" alt="" />
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <main id="home">
        <div class="banner-container">
            <div class="banner">
                <div class="type-container">
                    <div class="container">
                        <div class="mb-5 d-flex flex-column align-items-center gap-3 text-center">
                            <div class="">
                                <div class="" data-aos="fade-down" data-aos-duration="1500">
                                    قمر سمايا<br />
                                    <span class="orange">جمالك</span>، على
                                    <span class="orange">طريقتك</span>.
                                </div>
                            </div>

                            <div class="">
                                <img data-aos="fade-up" data-aos-duration="1500" class="banner-image"
                                    src="assets/imgs/logo/logo-light.svg" alt="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="devider case-slider">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
                        <path d="M500 80.7C358 68 0 4 0 4V0h1000v84.7c-216 23.3-358 8.6-500-4Z" opacity=".3"></path>
                        <path d="M500 65.7C358 53 0 4 0 4V0h1000v62.7c-216 23.3-358 15.6-500 3Z" opacity=".5"></path>
                        <path d="M500 50.7C358 38 0 4 0 4V0h1000v40.7C784 64 642 63.3 500 50.7Z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <section id="about-us" class="section section-0 light-bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 about">
                        <h4>من نحن؟</h4>
                        <p>
                            مرحبًا بك في قمر سمايا، منصتك الأساسية لكل ما يتعلق بالمكياج
                            والجمال والعناية بالبشرة. مهمتنا هي مساعدتك في الظهور والشعور
                            بأفضل ما لديك من خلال تقديم نصائح الخبراء، والدروس التعليمية،
                            وتوصيات المنتجات.
                        </p>
                        <p>
                            في قمر سمايا، نؤمن بتمكين الأفراد من احتضان جمالهم الفريد. سواء
                            كنت من عشاق المكياج، أو مبتدئًا في العناية بالبشرة، أو تبحث فقط
                            عن نصائح لتعزيز توهجك الطبيعي، فإننا هنا لمساعدتك. يتميز تطبيقنا
                            بأدلة شاملة، ودروس خطوة بخطوة، وتوصيات مخصصة لجعل رحلتك في
                            الجمال ممتعة وفعالة.
                        </p>
                        <p>
                            انضم إلى مجتمع قمر سمايا واستكشف عالمًا من نصائح وحيل الجمال بين
                            يديك. اكتشف منتجات جديدة، واتقن تقنيات المكياج، واحصل على بشرة
                            مثالية بسهولة. جمالك، على طريقتك.
                        </p>
                    </div>

                    <div class="col-lg-6 d-flex">
                        <img src="assets/imgs/mobile2.jpg" alt="" />
                    </div>
                </div>
            </div>
        </section>

        <section id="download" class="section section7 gray-1">
            <div class="devider case9">
                <svg id="Group_1" data-name="Group 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 33">
                    <path id="Path_1" data-name="Path 1"
                        d="M0,0V33c250,0,375-7.92,500-15.84C625,25.08,750,33,1000,33V0Z" opacity="0.5" />
                    <path id="Path_2" data-name="Path 2"
                        d="M0,0V1.231C250,1.231,375,8.615,500,16,625,8.615,750,1.231,1000,1.231V0Z" />
                </svg>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mobile-download-content">
                            <div class="title">
                                <span class="orange">حمّل</span> <span>التطبيق</span>
                            </div>
                            <div class="content">
                                استكشف عالمًا من المعرفة والتعليم بتحميل تطبيق قمر سمايا،
                                المتاح على جميع المنصات.
                            </div>

                            <div class="appdownload">
                                <div class="flex social-btns">
                                    <a class="app-btn blu flex vert" href="http://apple.com">
                                        <i class="fab fa-apple"></i>
                                        <p>
                                            متوفر على <br />
                                            <span class="big-txt">App Store</span>
                                        </p>
                                    </a>
                                    <a class="app-btn blu flex vert" href="http://google.com">
                                        <i class="fab fa-google-play"></i>
                                        <p>
                                            احصل عليه من <br />
                                            <span class="big-txt">Google Play</span>
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="assets/imgs/finder.png" alt="" />
                    </div>
                </div>
            </div>

            <div class="devider case1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
                    <path
                        d="M0 0v99.7C62 69 122.4 48.7 205 66c83.8 17.6 160.5 20.4 240-12 54-22 110-26 173-10a392.2 392.2 0 0 0 222-5c55-17 110.3-36.9 160-27.2V0H0Z"
                        opacity=".5"></path>
                    <path
                        d="M0 0v74.7C62 44 122.4 28.7 205 46c83.8 17.6 160.5 25.4 240-7 54-22 110-21 173-5 76.5 19.4 146.5 23.3 222 0 55-17 110.3-31.9 160-22.2V0H0Z">
                    </path>
                </svg>
            </div>
        </section>

        <div id="specs" class="section section-0-2 specs">
            <div class="pt-3 pb-5 text-center">
                <h2 class="has-color">
                    <b>ميزات <span>التطبيق</span></b>
                </h2>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">نصائح تجميل مخصصة</div>
                                <p>
                                    احصل على نصائح تجميل وعناية بالبشرة مخصصة وفقاً لاحتياجاتك.
                                    حقق أفضل مظهر لك من خلال نصائح الخبراء.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">دروس من الخبراء</div>
                                <p>
                                    احصل على دروس خطوة بخطوة من خبراء التجميل. تعلم تقنيات
                                    المكياج وروتينات العناية بالبشرة من أفضل العاملين في هذا
                                    المجال.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">توصيات المنتج</div>
                                <p>
                                    احصل على توصيات للمنتجات التي تناسب نوع بشرتك واحتياجاتك
                                    الجمالية. اكتشف أفضل المنتجات لتعزيز روتين جمالك.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">مشاركة المجتمع</div>
                                <p>
                                    انضم إلى مجتمع نشط من عشاق الجمال. شارك نصائحك، واحصل على
                                    ردود الفعل، وتواصل مع الآخرين الذين يشاركونك شغفك.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">استشارات افتراضية</div>
                                <p>
                                    احجز استشارات افتراضية مع أفضل خبراء التجميل والعناية
                                    بالبشرة. احصل على نصائح مهنية دون مغادرة منزلك.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="devider case2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
                <path d="M500 80.7C358 68 0 4 0 4V0h1000v84.7c-216 23.3-358 8.6-500-4Z" opacity=".3"></path>
                <path d="M500 65.7C358 53 0 4 0 4V0h1000v62.7c-216 23.3-358 15.6-500 3Z" opacity=".5"></path>
                <path d="M500 50.7C358 38 0 4 0 4V0h1000v40.7C784 64 642 63.3 500 50.7Z"></path>
            </svg>
        </div>

        <section id="faq" class="section section-faq section1">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h4>الأسئلة المتكررة</h4>
                    </div>
                    <div class="col-lg-12">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        ما هو قمر سمايا؟
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        قمر سمايا هو منصة شاملة مخصصة للجمال والمكياج والعناية
                                        بالبشرة. نحن نوفر للمستخدمين نصائح الخبراء، والدروس
                                        التعليمية، وتوصيات المنتجات لمساعدتهم على الظهور والشعور
                                        بأفضل حال.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true"
                                        aria-controls="collapseTwo">
                                        كيف أحجز استشارة افتراضية؟
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        حجز استشارة افتراضية أمر سهل. ببساطة قم بتحميل تطبيق قمر
                                        سمايا، واختر خبير التجميل المفضل لديك، وحدد الوقت المناسب
                                        لجلسة الاستشارة. يمكنك أيضًا الحجز عبر موقعنا الإلكتروني
                                        من خلال تسجيل الدخول إلى حسابك.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                        aria-expanded="true" aria-controls="collapseThree">
                                        ما هي الخدمات التي يقدمها قمر سمايا؟
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        يقدم قمر سمايا مجموعة واسعة من الخدمات بما في ذلك دروس
                                        المكياج، نصائح العناية بالبشرة، توصيات المنتجات،
                                        والاستشارات الافتراضية مع خبراء التجميل. هدفنا هو توفير كل
                                        الأدوات والمعرفة التي تحتاجها لتعزيز روتين جمالك.
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true"
                                        aria-controls="collapseFour">
                                        كيف يمكنني الانضمام إلى مجتمع قمر سمايا؟
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        الانضمام إلى مجتمع قمر سمايا أمر بسيط. قم بتحميل تطبيقنا
                                        وسجل لتتمكن من التواصل مع عشاق الجمال والخبراء. شارك
                                        نصائحك، واحصل على ردود الفعل، وابق على اطلاع على أحدث
                                        اتجاهات ومنتجات الجمال.
                                    </div>
                                </div>
                            </div> --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true"
                                        aria-controls="collapseFive">
                                        هل يمكنني الحصول على نصائح تجميل مخصصة؟
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        نعم، يوفر قمر سمايا نصائح تجميل وعناية بالبشرة مخصصة
                                        لاحتياجاتك الفردية. ببساطة املأ استبيانًا قصيرًا في
                                        التطبيق، وسيوفر لك خبراؤنا توصيات بناءً على ملفك الشخصي
                                        الفريد.
                                    </div>
                                </div>
                            </div>
                            <!-- Add more questions as needed -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="devider case1 dark">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
            <path
                d="M0 0v99.7C62 69 122.4 48.7 205 66c83.8 17.6 160.5 20.4 240-12 54-22 110-26 173-10a392.2 392.2 0 0 0 222-5c55-17 110.3-36.9 160-27.2V0H0Z"
                opacity=".5"></path>
            <path
                d="M0 0v74.7C62 44 122.4 28.7 205 46c83.8 17.6 160.5 25.4 240-7 54-22 110-21 173-5 76.5 19.4 146.5 23.3 222 0 55-17 110.3-31.9 160-22.2V0H0Z">
            </path>
        </svg>
    </div>
    <footer id="footer" class="footer gray-1">
        <div class="container">
            <div class="row">
                <a class="logo d-lg-none d-flex" href="#">
                    <img src="assets/imgs/logo/logo-light.svg" class="logo-light" alt=" " />
                </a>
                <div class="col-lg-6">
                    <h5>روابط مفيدة</h5>
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-item">
                            <a class="newLinkHover" href="{{ route('privacy_policy') }}">سياسة الخصوصية</a>
                        </li>
                        <li class="list-item">
                            <a class="newLinkHover" href="{{ route('terms_conditions') }}">الشروط والأحكام</a>
                        </li>

                        <!-- <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li> -->
                    </ul>
                </div>
                <!-- <div class="col-lg-4 col-sm-6">
                        <h5>SERVICES</h5>
                        <ul class="list-inline footer-link mb-0 ">
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                        </ul>
                    </div> -->
                <div class="col-lg-6">
                    <h5>اعثر علينا</h5>
                    <ul class="buttns list-inline footer-link mb-0">
                        <li class="list-item">
                            <a class="btn d-flex" href="https://maps.app.goo.gl/uxYyGZ7tV9Dso3c89?g_st=iw" target="_blank">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <div>
                                        <span>احصل على الاتجاهات</span>
                                        <p>سلمان الفارسي، الرياض</p>
                                    </div>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="btn d-flex" href="tel:+966551114415" target="_blank">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-telephone-fill"></i>
                                    <div>
                                        <span>اتصل بنا</span>
                                        <p class="text-ltr">+966 55 111 4415</p>
                                    </div>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="btn d-flex" href="mailto:qamarsamaya@gmail.com" target="_blank">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-envelope-fill"></i>
                                    <div>
                                        <span>البريد الإلكتروني</span>
                                        <p>qamarsamaya@gmail.com</p>
                                    </div>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-12">
                    <div class="footer-icon d-flex justify-content-center">
                        <a href="https://www.facebook.com/qamar.samaya.5">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/qamarsamaya">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/@qamarsamaya">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="https://wa.me/966551114415">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-12 text-center">
                    <hr />
                    <span>All rights reserved @ Qamar Samaya</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-parallax-js@5.5.1/dist/simpleParallax.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>
