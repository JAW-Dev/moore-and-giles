
// Require Modules
import creditCardType from 'credit-card-type';

class mgMerchanteSolutions {

  constructor() {
    jQuery(document).ready( function() {
      jQuery('body').on('keypress, keydown, keyup, paste, change, input', '#mg_mes-card-number', function() {
        let ccNum = jQuery('#mg_mes-card-number').val();
        let ccType = creditCardType(ccNum);
        let ccName = ccType[0].type;

        if ( ccName.length > 0 ) {
          jQuery('#wc-mg_mes-cc-form .wc-credit-card-type').val( ccName );
        }
      });
    });
  }
}

new mgMerchanteSolutions();
