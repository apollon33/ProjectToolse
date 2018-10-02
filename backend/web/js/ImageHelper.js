var ImageHelper = {
    map: {},

    _map: function() {
        var $this = this;
        $this.map = {
            fileInput: 'input[type="file"].file-loading',
            fileHidden: null
        };
    },

    init: function() {
        var $this = this;
        $this._map();

        if ($this.map.fileInput.length > 0) {
            $this.initFileName();
        }
    },

    initFileName: function() {
        var $this = this,
            fileInput = $($this.map.fileInput),
            inputName = fileInput.attr('name'),
            inputValue = fileInput.attr('value');
        
        $this.map.fileHidden = $('input[type="hidden"][name="' + inputName + '"]');
        $this.map.fileHidden.val(inputValue);

        return false;
    },

    clearInputName: function() {
        var $this = this,
            fileInput = $($this.map.fileInput);

        fileInput.val(null);
        $this.map.fileHidden.val(null);

        return false;
    }
};

$(function() {
    ImageHelper.init();
});
