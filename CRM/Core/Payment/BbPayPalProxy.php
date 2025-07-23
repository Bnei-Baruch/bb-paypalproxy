<?php

require_once 'CRM/Core/Payment.php';

/**
 * PayPal Proxy Payment Processor
 * 
 * Acts as a proxy to the existing PayPal payment processor,
 * transferring all parameters and functionality.
 */
class CRM_Core_Payment_BbPayPalProxy extends CRM_Core_Payment {
    protected $_mode = NULL;
    protected $_params = [];

    /**
     * Constructor.
     */
    public function __construct($mode, &$paymentProcessor) {
        $this->_mode = $mode;
        $this->_paymentProcessor = $paymentProcessor;
        $this->_setParam('processorName', 'PayPal Proxy');
    }

    /**
     * This function checks to see if we have the right config values.
     */
    public function checkConfig() {
        $error = array();

        if (empty($this->_paymentProcessor["user_name"])) {
            $error[] = ts("PayPal Email is not set in the PayPal Proxy Payment Processor settings.");
        }

        if (empty($this->_paymentProcessor["signature"])) {
            $error[] = ts("Target PayPal Processor is not selected in the PayPal Proxy settings.");
        } else {
            // Validate that the selected processor exists and is active
            try {
                $targetProcessor = civicrm_api3('PaymentProcessor', 'getsingle', [
                    'id' => $this->_paymentProcessor["signature"],
                    'is_active' => 1
                ]);
            } catch (Exception $e) {
                $error[] = ts("Selected Target PayPal Processor (ID: %1) is not found or inactive.", [1 => $this->_paymentProcessor["signature"]]);
            }
        }

        if (!empty($error)) {
            return implode("<p>", $error);
        } else {
            return NULL;
        }
    }

    /**
     * Get payment instrument ID
     */
    public function getPaymentInstrumentID() {
        return 1; // Credit Card
    }

    /**
     * Process payment
     */
    function doPayment(&$params, $component = 'contribute') {
        if ($component != 'contribute' && $component != 'event') {
            CRM_Core_Error::debug_log_message("Component '{$component}' is invalid.");
            CRM_Utils_System::civiExit();
        }

        $this->_component = $component;
        $statuses = CRM_Contribute_BAO_Contribution::buildOptions('contribution_status_id', 'validate');

        $invoiceID = $this->_getParam('invoiceID');
        $contributionID = $params['contributionID'] ?? NULL;

        if ($this->checkDupe($invoiceID, $contributionID)) {
            throw new PaymentProcessorException('It appears that this transaction is a duplicate.', 9004);
        }

        // If we have a $0 amount, skip call to processor and set payment_status to Completed.
        if ($params['amount'] == 0) {
            $result = array();
            $result['payment_status_id'] = array_search('Completed', $statuses);
            $result['payment_status'] = 'Completed';
            return $result;
        }

        // Handle currency conversion
        $currencyName = $params['custom_1706'] ?? $params['currencyID'] ?? 'ILS';
        if ($currencyName === 'NIS') {
            $currencyName = 'ILS';
        }
        $params['currencyID'] = $currencyName;

	// Get the target PayPal processor ID from signature field
	$targetProcessorId = $this->_paymentProcessor["signature"];
        try {
            // Get the target PayPal processor
            $targetProcessor = Civi\Payment\System::singleton()->getById($targetProcessorId);
            
            if (!$targetProcessor) {
                throw new PaymentProcessorException('Target PayPal processor not found: ' . $targetProcessorId, 9005);
            }

            // Forward the payment to the target processor
            return $targetProcessor->doPayment($params, $component);
            
        } catch (Exception $e) {
            CRM_Core_Error::debug_log_message("PayPal Proxy error: " . $e->getMessage());
            throw new PaymentProcessorException('Payment processing failed: ' . $e->getMessage(), 9006);
        }
    }

    /**
     * Get the value of a field if set.
     */
    public function _getParam($field, $xmlSafe = FALSE) {
        $value = $this->_params[$field] ?? '';
        if ($xmlSafe) {
            $value = str_replace(['&', '"', "'", '<', '>'], '', $value);
        }
        return $value;
    }

    /**
     * Set a field to the specified value.
     */
    public function _setParam($field, $value) {
        $this->_params[$field] = $value;
    }

    /**
     * Get currency support
     */
    public function getSupportedCurrencyCodes() {
        return [
            'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY',
            'CHF', 'NOK', 'SEK', 'DKK', 'PLN', 'HUF',
            'CZK', 'ILS', 'MXN', 'BRL', 'SGD', 'HKD',
            'TWD', 'THB', 'TRY', 'NZD'
        ];
    }

    /**
     * Can the processor support recurring contributions?
     */
    public function supportsRecurring() {
        return TRUE;
    }

    /**
     * Can the processor support edit recurring contributions?
     */
    public function supportsEditRecurringContribution() {
        return FALSE;
    }

    /**
     * Can the processor support cancel recurring contributions?
     */
    public function supportsCancelRecurring() {
        return TRUE;
    }

    /**
     * Does this processor support pre-approval?
     */
    public function supportsPreApproval() {
        return FALSE;
    }

    /**
     * Get billing mode
     */
    public function getBillingMode() {
        return CRM_Core_Payment::BILLING_MODE_NOTIFY;
    }

    /**
     * Get payment type label
     */
    public function getPaymentTypeLabelTranslated() {
        return 'PayPal Proxy';
    }
}
