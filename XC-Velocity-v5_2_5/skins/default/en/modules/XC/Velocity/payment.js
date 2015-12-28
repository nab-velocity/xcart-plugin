/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Velocity initialize
 *
 * @author    Velocity Team
 * @copyright Copyright (c) 2015-2016 Velocity. All rights reserved
 * @license   
 * @link      http://nabvelocity.com/
 */

core.bind(
  'checkout.main.initialize',
  function() {

    core.bind(
      'checkout.common.ready',
      function(event, state) {
        var box = jQuery('.velocity-widget');

        if (box.length) {
            var firstname = $('#shippingaddress-firstname').val();
            var lastname = $('#shippingaddress-lastname').val();
            var city = $('#shippingaddress-city').val();
            var country = $('#shippingaddress-country-code').val();
            var address = $('#shippingaddress-street').val();
            var addressStateId = $('#shippingaddress-state-id').is(':visible')
                ? $('#shippingaddress-state-id').val()
                : '';
            var addressState = '';
            var zip = $('#shippingaddress-zipcode').val();


            if (!$('#same_address').prop('checked')) {
                firstname = $('#billingaddress-firstname').val();
                lastname = $('#billingaddress-lastname').val();
                city = $('#billingaddress-city').val();
                country = $('#billingaddress-country-code').val();
                address = $('#billingaddress-street').val();
                addressStateId = $('#billingaddress-state-id').is(':visible')
                    ? $('#billingaddress-state-id').val()
                    : '';
                zip = $('#billingaddress-zipcode').val();
            }

            if ('' !== addressStateId) {
                var states = $('#state-codes-data').data('stateCodes');
                for (var i = 0; i < states.length; i++) {
                    if (states[i].id == addressStateId ) {
                        addressState = states[i].code;
                        break;
                    }
                }
            }

            var cardInfo = {
                number:         box.find('#cc_number').val(),
                cardtype:       box.find('#card_type').val(),        
                cvc:            box.find('#cc_cvv2').val(),
                expMonth:       box.find('#cc_expire_month').val(),
                expYear:        box.find('#cc_expire_year').val(),
                addressCity:    city,
                addressCountry: country,
                addressLine1:   address,
                addressZip:     zip,
                name:           firstname + ' ' + lastname
            }
            if ('' !== addressState && 2 >= addressState.length) {
                cardInfo.addressState = addressState;
            }
            cardInfoEc = base64_encode(JSON.stringify(cardInfo));
            $('#token').val(cardInfoEc);
            $('form.place').submit();

        }
      }
    );

    core.bind(
      'checkout.common.anyChange',
      function(event, state) {
        var box = jQuery('.velocity-widget');
        if (box.length) {

          var firstname = $('#shippingaddress-firstname').val();
          var lastname = $('#shippingaddress-lastname').val();

          if (!$('#same_address').prop('checked')) {
            firstname = $('#billingaddress-firstname').val();
            lastname = $('#billingaddress-lastname').val();
          }

          box.find('#cc_name').val(firstname + ' ' + lastname);
        }
      }
    );
  }
);
  
// base64 encoding any data string	
function base64_encode(data) {

  var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        enc = '',
        tmp_arr = [];

  if (!data) {
        return data;
  }

  data = unescape(encodeURIComponent(data));

  do {
        // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);

        bits = o1 << 16 | o2 << 8 | o3;

        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;

        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
  } while (i < data.length);

  enc = tmp_arr.join('');

  var r = data.length % 3;

  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
}