<?php

function add_gtm_head_script()
{
    ?>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5K589JG');</script>
    <!-- End Google Tag Manager -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-19372249-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-19372249-1');
    </script>

    <script type='application/ld+json'>
    {
      "@context": "http://www.schema.org",
      "@type": "LegalService",
      "telephone": "1300 132 088",
      "priceRange": "Market Value",
      "name": "KRG Conveyancing ",
      "url": "https://www.krg.com.au/",
      "sameAs": [
        "https://twitter.com/conveyancingqld",
        "https://www.instagram.com/krgconveyancing/",
        "https://www.productreview.com.au/listings/krg-conveyancing",
        "https://www.linkedin.com/company/krg-conveyancing",
        "https://www.facebook.com/pages/KRG-Conveyancing/193978513958633"
      ],
      "logo": "<?= TEMPLATE_DIRECTORY_URL ?>/assets/images/logo.svg",
      "image": "<?= TEMPLATE_DIRECTORY_URL ?>/assets/images/home-slider-image-young-couple.jpg",
      "description": "KRG Conveyancing are Queensland’s property conveyancing specialists. From our offices in Brisbane and the Gold Coast (Southport), our experienced solicitors service all areas of Queensland, providing a high-quality, low-cost and hassle-free conveyancing service backed by guaranteed fixed prices and our 100% no-move no-fee policy. We make buying and selling property stress-free.",
      "address":
        [
        {
        "@type": "PostalAddress",
        "streetAddress": "27/480 Queen Street",
        "addressLocality": "Brisbane City",
        "addressRegion": "QLD",
        "postalCode": "4000",
        "addressCountry": "Australia"
      },
       {
        "@type": "PostalAddress",
        "streetAddress": "9 Bay Street",
        "addressLocality": "Southport",
        "addressRegion": "QLD",
        "postalCode": "4215",
        "addressCountry": "Australia"
      }
      ],
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "-27.4648167",
        "longitude": "153.028877"
      },
      "hasMap": "https://www.google.com.au/maps/place/KRG+Conveyancing+Brisbane/@-27.4648167,153.028877,17z/data=!3m1!4b1!4m5!3m4!1s0x6b915a1879c00029:0xdbe4deae84b88ac7!8m2!3d-27.464944!4d153.028417",
      "openingHours": "Mo, Tu, We, Th, Fr 09:30-16:30"
    }
    </script>

    <script type='application/ld+json'>
    {
      "@context": "http://www.schema.org",
      "@type": "Organization",
      "name": "KRG Conveyancing",
      "logo": "<?= TEMPLATE_DIRECTORY_URL ?>/assets/images/logo.svg",
      "url": "https://www.krg.com.au/",
       "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "5",
        "worstRating": "3",
        "bestRating": "5",
        "reviewCount": "117"
      },
      "legalName": "KRG CONVEYANCING CENTRE PTY LTD",
      "leiCode" : "ABN 33 141 947 186",
      "isicV4": "7310",
      "mainEntityOfPage": "LegalService" 
    }
     </script>
    <?php
}
add_action('wp_head', 'add_gtm_head_script');


function add_gtm_body_script()
{
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5K589JG" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('wp_body_open', 'add_gtm_body_script');