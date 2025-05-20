<?php

namespace Faysal0x1\LaraPayment\SslCommerz;

class SslCommerzNotification extends AbstractSslCommerz
{
    protected $data = [];
    protected $config = [];

    private $successUrl;
    private $cancelUrl;
    private $failedUrl;
    private $ipnUrl;
    private $error;

    /**
     * @var string $baseUrl
     */
    private $baseUrl;
    /**
     * @var array $paymentUrl
     */
    private $paymentUrl=[];

    /**
     * SslCommerzNotification constructor.
     */
    public function __construct()
    {
        $this->config = config('sslcommerz');

        $this->setStoreId($this->config['store_id']);
        $this->setStorePassword($this->config['store_password']);
        $this->baseUrl();
        ## default info
        $this->defaultInfo();
        return $this;
    }

    /**
     * sslcommerz Base Url
     * if sandbox is true it will be sandbox url otherwise it is host url
     */
    private function baseUrl()
    {
        if ($this->config['sandbox'] == true) {
            $this->baseUrl = 'https://sandbox.sslcommerz.com';
        } else {
            $this->baseUrl = 'https://securepay.sslcommerz.com';
        }
        $this->paymentUrl();
        return $this;
    }

    private function paymentUrl()
    {
        $this->paymentUrl = [
            'make_payment' => $this->baseUrl."/gwprocess/v4/api.php",
            'transaction_status' => $this->baseUrl."/validator/api/merchantTransIDvalidationAPI.php",
            'order_validate' => $this->baseUrl."/validator/api/validationserverAPI.php",
            'refund_payment' => $this->baseUrl."/validator/api/merchantTransIDvalidationAPI.php",
            'refund_status' => $this->baseUrl."/validator/api/merchantTransIDvalidationAPI.php",
        ];
        return $this;
    }

    public function orderValidate($post_data, $trx_id = '', $amount = 0, $currency = "BDT")
    {
        if ($post_data == '' && $trx_id == '' && !is_array($post_data)) {
            $this->error = "Please provide valid transaction ID and post request data";
            return $this->error;
        }

        return $this->validate($trx_id, $amount, $currency, $post_data);
    }

    # VALIDATE SSLCOMMERZ TRANSACTION
    protected function validate($merchant_trans_id, $merchant_trans_amount, $merchant_trans_currency, $post_data)
    {
        # MERCHANT SYSTEM INFO
        if (!empty($merchant_trans_id) && !empty($merchant_trans_amount)) {

            # CALL THE FUNCTION TO CHECK THE RESULT
            $post_data['store_id'] = $this->getStoreId();
            $post_data['store_pass'] = $this->getStorePassword();

            $val_id = urlencode($post_data['val_id']);
            $store_id = urlencode($this->getStoreId());
            $store_passwd = urlencode($this->getStorePassword());
            $requested_url = ($this->paymentUrl['order_validate'] . "?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json");

            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $requested_url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

            if ($this->config['sandbox']) {
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
            } else {
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 2);
            }

            $result = curl_exec($handle);
            $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            if ($code == 200 && !(curl_errno($handle))) {
                $result = json_decode($result);
                $this->sslc_data = $result;

                # TRANSACTION INFO
                $status = $result->status;
                $tran_date = $result->tran_date;
                $tran_id = $result->tran_id;
                $val_id = $result->val_id;
                $amount = $result->amount;
                $store_amount = $result->store_amount;
                $bank_tran_id = $result->bank_tran_id;
                $card_type = $result->card_type;
                $currency_type = $result->currency_type;
                $currency_amount = $result->currency_amount;

                # ISSUER INFO
                $card_no = $result->card_no;
                $card_issuer = $result->card_issuer;
                $card_brand = $result->card_brand;
                $card_issuer_country = $result->card_issuer_country;
                $card_issuer_country_code = $result->card_issuer_country_code;

                # API AUTHENTICATION
                $APIConnect = $result->APIConnect;
                $validated_on = $result->validated_on;
                $gw_version = $result->gw_version;

                # GIVE SERVICE
                if ($status == "VALID" || $status == "VALIDATED") {
                    if ($merchant_trans_currency == "BDT") {
                        if (trim($merchant_trans_id) == trim($tran_id) && (abs($merchant_trans_amount - $amount) < 1) && trim($merchant_trans_currency) == trim('BDT')) {
                            return true;
                        } else {
                            # DATA TEMPERED
                            $this->error = "Data has been tempered";
                            return false;
                        }
                    } else {
                        if (trim($merchant_trans_id) == trim($tran_id) && (abs($merchant_trans_amount - $currency_amount) < 1) && trim($merchant_trans_currency) == trim($currency_type)) {
                            return true;
                        } else {
                            # DATA TEMPERED
                            $this->error = "Data has been tempered";
                            return false;
                        }
                    }
                } else {
                    # FAILED TRANSACTION
                    $this->error = "Failed Transaction";
                    return false;
                }
            } else {
                # Failed to connect with SSLCOMMERZ
                $this->error = "Faile to connect with SSLCOMMERZ";
                return false;
            }
        } else {
            # INVALID DATA
            $this->error = "Invalid data";
            return false;
        }
    }

    # FUNCTION TO CHECK HASH VALUE
    protected function SSLCOMMERZ_hash_verify($post_data, $store_passwd = "")
    {
        if (isset($post_data) && isset($post_data['verify_sign']) && isset($post_data['verify_key'])) {
            # NEW ARRAY DECLARED TO TAKE VALUE OF ALL POST
            $pre_define_key = explode(',', $post_data['verify_key']);

            $new_data = array();
            if (!empty($pre_define_key)) {
                foreach ($pre_define_key as $value) {
                    $new_data[$value] = ($post_data[$value]);
                }
            }
            # ADD MD5 OF STORE PASSWORD
            $new_data['store_passwd'] = md5($store_passwd);

            # SORT THE KEY AS BEFORE
            ksort($new_data);

            $hash_string = "";
            foreach ($new_data as $key => $value) {
                $hash_string .= $key . '=' . ($value) . '&';
            }
            $hash_string = rtrim($hash_string, '&');

            if (md5($hash_string) == $post_data['verify_sign']) {
                return true;
            } else {
                $this->error = "Verification signature not matched";
                return false;
            }
        } else {
            $this->error = 'Required data mission. ex: verify_key, verify_sign';
            return false;
        }
    }

    /**
     * @param array $requestData
     * @param string $type
     * @param string $pattern
     * @return false|mixed|string
     */
    public function makePayment(array $requestData, $type = 'checkout', $pattern = 'json')
    {
        if (empty($requestData)) {
            return "Please provide a valid information list about transaction with transaction id, amount, success url, fail url, cancel url, store id and pass at least";
        }

        $header = [];

        $this->setApiUrl($this->paymentUrl['make_payment']);

        // Set the compulsory params
        $this->setRequiredInfo($requestData);

        // Set the authentication information
        $this->setAuthenticationInfo();

        // Now, call the Gateway API
        $response = $this->callToApi($this->data, $header, $this->config['sandbox']);

        $formattedResponse = $this->formatResponse($response, $type, $pattern);

        if ($type == 'hosted') {
            if (!empty($formattedResponse['GatewayPageURL'])) {
                $this->redirect($formattedResponse['GatewayPageURL']);
            } else {
                if (strpos($formattedResponse['failedreason'], 'Store Credential') === false) {
                    $message = $formattedResponse['failedreason'];
                } else {
                    $message = "Check the SSLCZ_TESTMODE and SSLCZ_STORE_PASSWORD value in your .env; DO NOT USE MERCHANT PANEL PASSWORD HERE.";
                }

                return $message;
            }
        } else {
            return $formattedResponse;
        }
    }

    private function setSuccessUrl() {
        $this->successUrl = url('/') . $this->config['success_url'];
        return $this;
    }

    private function setFailedUrl() {
        $this->failedUrl = url('/') . $this->config['failed_url'];
        return $this;
    }

    private function setCancelUrl() {
        $this->cancelUrl = url('/') . $this->config['cancel_url'];
    }

    private function setIPNUrl() {
        $this->ipnUrl = url('/') . $this->config['ipn_url'];
        return $this;
    }

    private function setAuthenticationInfo()
    {
        $this->data['store_id'] = $this->getStoreId();
        $this->data['store_passwd'] = $this->getStorePassword();
        return $this;
    }

    private function setRequiredInfo(array $info)
    {
        $this->data['total_amount'] = $info['total_amount'];
        $this->data['currency'] = $info['currency'];
        $this->data['tran_id'] = $info['tran_id'];
        $this->data['product_category'] = (isset($info['product_category'])) ? $info['product_category'] : 'Our Product';

        return $this;
    }

    private function defaultInfo(){
        // Set the SUCCESS, FAIL, CANCEL Redirect URL before setting the other parameters
        $this->setSuccessUrl();
        $this->setFailedUrl();
        $this->setCancelUrl();
        $this->setIPNUrl();

        $this->data['success_url'] = $this->successUrl;
        $this->data['fail_url'] = $this->failedUrl;
        $this->data['cancel_url'] = $this->cancelUrl;
        $this->data['ipn_url'] = $this->ipnUrl;

        $this->data['multi_card_name'] = (isset($info['multi_card_name'])) ? $info['multi_card_name'] : null;
        $this->data['allowed_bin'] = (isset($info['allowed_bin'])) ? $info['allowed_bin'] : null;

        ##   Parameters to Handle EMI Transaction ##
        $this->data['emi_option'] = (isset($info['emi_option'])) ? $info['emi_option'] : null;
        $this->data['emi_max_inst_option'] = (isset($info['emi_max_inst_option'])) ? $info['emi_max_inst_option'] : null;
        $this->data['emi_selected_inst'] = (isset($info['emi_selected_inst'])) ? $info['emi_selected_inst'] : null;
        $this->data['emi_allow_only'] = (isset($info['emi_allow_only'])) ? $info['emi_allow_only'] : 0;

        # CUSTOMER INFORMATION
        $this->data['cus_name'] = 'Customer Name';
        $this->data['cus_email'] = 'customer@mail.com';
        $this->data['cus_add1'] = 'Customer Address';
        $this->data['cus_add2'] = "";
        $this->data['cus_city'] = "";
        $this->data['cus_state'] = "";
        $this->data['cus_postcode'] = "";
        $this->data['cus_country'] = "Bangladesh";
        $this->data['cus_phone'] = '8801XXXXXXXXX';
        $this->data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $this->data['ship_name'] = "Store Test";
        $this->data['ship_add1'] = "Dhaka";
        $this->data['ship_add2'] = "Dhaka";
        $this->data['ship_city'] = "Dhaka";
        $this->data['ship_state'] = "Dhaka";
        $this->data['ship_postcode'] = "1000";
        $this->data['ship_phone'] = "";
        $this->data['ship_country'] = "Bangladesh";

        $this->data['shipping_method'] = "NO";
        $this->data['product_name'] = "Computer";
        $this->data['product_category'] = "Goods";
        $this->data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $this->data['value_a'] = "ref001";
        $this->data['value_b'] = "ref002";
        $this->data['value_c'] = "ref003";
        $this->data['value_d'] = "ref004";
        return $this;
    }

    public function setCustomerInfo(array $info)
    {
        $this->data['cus_name'] = (isset($info['name'])) ? $info['name'] : 'Ab Karim';
        $this->data['cus_email'] = (isset($info['email'])) ? $info['email'] : 'customer@email.com';
        $this->data['cus_add1'] = (isset($info['address_1'])) ? $info['address_1'] : 'Dhaka';
        $this->data['cus_add2'] = (isset($info['address_2'])) ? $info['address_2'] : '';
        $this->data['cus_city'] = (isset($info['city'])) ? $info['city'] : 'Dhaka';
        $this->data['cus_state'] = (isset($info['state'])) ? $info['state'] : null;
        $this->data['cus_postcode'] = (isset($info['postcode'])) ? $info['postcode'] : null;
        $this->data['cus_country'] = (isset($info['country'])) ? $info['country'] : 'Bangladesh';
        $this->data['cus_phone'] = (isset($info['phone'])) ? $info['phone'] : '015XXXXXXXX';
        $this->data['cus_fax'] = (isset($info['fax'])) ? $info['fax'] : null;

        return $this;
    }

    public function setShipmentInfo(array $info)
    {
        $this->data['shipping_method'] = (isset($info['shipping_method'])) ? $info['shipping_method'] : 'Yes';
        $this->data['num_of_item'] = isset($info['num_of_item']) ? $info['num_of_item'] : 1;
        $this->data['ship_name'] = (isset($info['ship_name'])) ? $info['ship_name'] : '';
        $this->data['ship_add1'] = (isset($info['ship_add1'])) ? $info['ship_add1'] : '';
        $this->data['ship_add2'] = (isset($info['ship_add2'])) ? $info['ship_add2'] : null;
        $this->data['ship_city'] = (isset($info['ship_city'])) ? $info['ship_city'] : 'Dhaka';
        $this->data['ship_state'] = (isset($info['ship_state'])) ? $info['ship_state'] : null;
        $this->data['ship_postcode'] = (isset($info['ship_postcode'])) ? $info['ship_postcode'] : null;
        $this->data['ship_country'] = (isset($info['ship_country'])) ? $info['ship_country'] : null;

        return $this;
    }

    public function setAdditionalInfo(array $info)
    {
        $this->data['value_a'] = (isset($info['value_a'])) ? $info['value_a'] : null;
        $this->data['value_b'] = (isset($info['value_b'])) ? $info['value_b'] : null;
        $this->data['value_c'] = (isset($info['value_c'])) ? $info['value_c'] : null;
        $this->data['value_d'] = (isset($info['value_d'])) ? $info['value_d'] : null;

        return $this;
    }

    public function setBin(string $bin)
    {
        $this->data['allowed_bin'] = $bin;
        return $this;
    }

    public function enableEMI(int $installment, int $max_installment, bool $restrict_emi_only = false)
    {
        $this->data['emi_option'] = 1;
        $this->data['emi_selected_inst'] = $installment;
        $this->data['emi_max_inst_option'] = $max_installment;
        $this->data['emi_allow_only'] = $restrict_emi_only ? 1 : 0;

        return $this;
    }

    public function setAirlineTicketProfile(array $info)
    {
        $this->data['product_profile'] = 'airline-tickets';
        $this->data['hours_till_departure'] = (isset($info['hours_till_departure'])) ? $info['hours_till_departure'] : '';
        $this->data['flight_type'] = (isset($info['flight_type'])) ? $info['flight_type'] : null;
        $this->data['pnr'] = (isset($info['pnr'])) ? $info['pnr'] : null;
        $this->data['journey_from_to'] = (isset($info['journey_from_to'])) ? $info['journey_from_to'] : 'Dhaka';
        $this->data['third_party_booking'] = (isset($info['third_party_booking'])) ? $info['third_party_booking'] : '';

        return $this;
    }

    public function setTravelVerticalProfile(array $info)
    {
        $this->data['product_profile'] = 'travel-vertical';
        $this->data['hotel_name'] = (isset($info['hotel_name'])) ? $info['hotel_name'] : '';
        $this->data['length_of_stay'] = (isset($info['length_of_stay'])) ? $info['length_of_stay'] : 1;
        $this->data['check_in_time'] = (isset($info['check_in_time'])) ? $info['check_in_time'] : null;
        $this->data['hotel_city'] = (isset($info['hotel_city'])) ? $info['hotel_city'] : 'Dhaka';

        return $this;
    }

    public function setTelecomVerticleProfile(array $info)
    {
        $this->data['product_profile'] = 'telecom-vertical';
        $this->data['product_type'] = (isset($info['product_type'])) ? $info['product_type'] : null;
        $this->data['topup_number'] = (isset($info['topup_number'])) ? $info['topup_number'] : null;
        $this->data['country_topup'] = (isset($info['country_topup'])) ? $info['country_topup'] : null;

        return $this;
    }

    public function setCarts(array $info)
    {
        $this->data['cart'] = (isset($info['cart'])) ? $info['cart'] : null;
        $this->data['product_amount'] = (isset($info['product_amount'])) ? $info['product_amount'] : 0;
        $this->data['vat'] = (isset($info['vat'])) ? $info['vat'] : null;
        $this->data['discount_amount'] = (isset($info['discount_amount'])) ? $info['discount_amount'] : 0;
        $this->data['convenience_fee'] = (isset($info['convenience_fee'])) ? $info['convenience_fee'] : 0;
        return $this;
    }

    public function returnSuccess($transId, $message, $url='/')
    {
        if ($this->config['return_response'] == 'html') {
            return view('sslcommerz::success', compact('transId', 'message', 'url'));
        }
        return response()->json(['status'=>'success', 'transaction_id'=>$transId, 'message'=>$message, 'return_url'=>$url], 200);
    }

    public function returnFail($transId, $message, $url='/')
    {
        if ($this->config['return_response'] == 'html') {
            return view('sslcommerz::failed', compact('transId', 'message', 'url'));
        }
        return response()->json(['status'=>'error', 'transaction_id'=>$transId, 'message'=>$message, 'return_url'=>$url], 404);
    }
} 