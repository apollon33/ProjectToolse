var PaymentHelper={

    map:{},

    _map: function() {
        var $this = this;
        $this.map = {
            payment:'#payment-amount,#payment-tax_profit,#payment-tax_war,#payment-tax_pension',
        };
    },

    init: function() {
        var $this = this;
        $this._map();
        $($this.map.payment).keyup(function(){ return $this.paymentSUM()});
    },

    paymentSUM: function() {
        $('#payment-payout').val($('#payment-amount').val()-
            $('#payment-tax_profit').val()-
            $('#payment-tax_war').val()-
            $('#payment-tax_pension').val());
    }

};
$(function() {
    PaymentHelper.init();
});
