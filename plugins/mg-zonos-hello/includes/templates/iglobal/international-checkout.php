<?php
  if ( ! defined( 'ABSPATH' ) ) exit;

  $store_id = get_option('iglobal_store_id');
  $store_key = get_option('iglobal_store_key');
  $subdomain = get_option('iglobal_subdomain', 'checkout');

  $store = new IgWC($store_id, $store_key);

  $cart = WC()->cart;

  if ( !$cart ) {

  } else {

    if(get_option('iglobal_is_active') == 'disabled'){
     header('Location: '.wc_get_checkout_url());
    }
    $countryCode = '';
    if(isset($_COOKIE['zCountry'])){
      $countryCode = $_COOKIE['zCountry'];
      if(in_array($countryCode, get_option('iglobal_domestic_countries'))){
          header('Location: '.wc_get_checkout_url());
      }
    }

    if(is_user_logged_in()){
      $user = get_current_user_id();
    } else {
      $user = '';
    }

    $tempId = $store->zonos_create_temp_cart( $cart, get_home_url()."/iglobal/success", $user );
    $clientId = '';

    if(isset($_COOKIE['ig_clientId'])){
      $clientId = $_COOKIE['igClientId'];
    }
    $url = 'https://'.$subdomain.'.iglobalstores.com?store='.$store_id.'&tempCartUUID='.$tempId.'&country='.$countryCode.'&clientId='.$clientId;
    ?>
      <html><head> <title><?php echo wp_get_document_title(); ?></title>
        <style type="text/css"> body { margin: 0; } #content{position:absolute; margin: 0px; overflow: hidden; -webkit-overflow-scrolling:touch; overflow-y: scroll; height: 100vh; width: 100vw;} </style>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script>
            window.onload = function() {
                if(!window.location.hash) {
                    window.location = window.location + '#zc';
                    window.location.reload();
                }else{
                    var iframe = document.createElement('iframe');
                    iframe.src = "<?php echo $url ?>";
                    iframe.width = '100%';
                    iframe.height = '100%';
                    document.body.appendChild(iframe);
                }
            }
        </script>
      </head><body></body></html>
    <?php
  }

