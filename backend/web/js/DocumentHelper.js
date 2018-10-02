'use strict'

var DocumentHelper = {


    url: {
        deleteDiscussionMessage: '/buildingprocess/deal/delete-discussion-message',
        discussionUpdate: '/buildingprocess/deal/discussion-update',
    },

    accessType: {
        USER: 1,
    },

    selectors: {
        body: 'body',
        readTextBlock: '#read-text',
        readMoreBlock: '#read-more',
        readMoreLink: '#read-more a',
        typeLink: '#select-type',
        kvTree: '.kv-tree',
        kvDetailContainer: '.kv-detail-container',
        formGroup: '.form-group',
        permission: '.permission',
        assignCheck: 'input.assign',
        listSelectors: '#list',
        discussionSaveButton: '.js-discussion-document-tree-save',
        discussionForm: '.js-discussion-form',
        dynamicDiscussionContent: '.js-dynamic-discussion-content',
        deleteDiscussionMessageButton: '.js-delete-discussion-message',
        modelDocumentId: '#modelDocumentId',
        textViewDocument: '.text-view-document',
        copyUrl: '.copy-url'
    },

    elements: {},

    _initElements: function () {
        this.elements = {
            body: $(this.selectors.body),
            list: $(this.selectors.listSelectors),
            kvDetailContainer: $(this.selectors.kvDetailContainer),
        };
    },

    init: function () {
        var $this = this;
        $this._initElements();
        $this.elements.body.on('click', $this.selectors.readMoreLink, function () {
            return $this.readMoreClick(this);
        });
        $this.elements.body.on('click', $this.selectors.kvTree, function () {
            $this.elements.kvDetailContainer.find('.alert').removeClass('hide');
        });
        $this.elements.body.on('change', $this.selectors.typeLink, function () {
            return $this.activeType()
        });
        $this.elements.body.on('click', $this.selectors.discussionSaveButton, function () {
            $this.showNewDiscussionMessage();
        });
        $this.elements.body.on('click', $this.selectors.deleteDiscussionMessageButton, function () {
            var messageId = $(this).attr('id');
            $this.deleteDiscussionMessage(messageId);
        });

        $this.elements.body.on('click', $this.selectors.copyUrl, function () {
            event.preventDefault();
            $this.copyLinkDocument(this);
        })
    },

    copyLinkDocument : function (list) {
        var copyLinkDocument = list.href,
            temp = $("<input>");
        $("div.copy-link-document").append(temp);
        temp.val(copyLinkDocument).select();
        document.execCommand("copy");
        temp.remove();
    },

    readMoreClick: function (element) {
        var $this = this;

        $($this.selectors.textViewDocument).removeClass('text-view-document')
        $($this.selectors.readMoreBlock).remove();

        return false;
    },

    activeType: function () {
        var $this = this;
        $.ajax({
            url: 'document/type',
            type: 'get',
            data: {id: $($this.selectors.typeLink).val()},
            dataType: 'json',
            cache: false,
            success: function (listArray) {
                var list = $($this.selectors.listSelectors),
                    selectedList = list.val(),
                    type = $($this.selectors.typeLink).val();

                $($this.selectors.permission).removeClass('hidden');

                if (parseInt(type) !== $this.accessType.USER) {
                    $($this.selectors.assignCheck).parent().addClass('hidden');
                } else {
                    $($this.selectors.assignCheck).parent().removeClass('hidden');
                }

                list.empty().append($('<option >', {text: 'Select...', value: ''}));
                $.each(listArray, function (key, item) {
                    list.append($('<option >', {
                        value: key,
                        text: item,
                        selected: selectedList == item
                    }));
                });
            }
        });
    },

    showNewDiscussionMessage: function () {
        var $this = this, data = $($this.selectors.discussionForm).serialize();
        $.ajax({
            url: $this.url.discussionUpdate,
            type: 'POST',
            dataType: 'json',
            data: data,
        })
            .done(function (result) {
                $($this.selectors.dynamicDiscussionContent).html(result.view);
            })

    },
    deleteDiscussionMessage: function (messageId) {
        var $this = this, data = {
            messageId: messageId,
            modelDocumentId: $($this.selectors.modelDocumentId).val()
        };
        $.ajax({
            url: $this.url.deleteDiscussionMessage,
            type: 'POST',
            dataType: 'json',
            data: data
        })
            .done(function (result) {
                $($this.selectors.dynamicDiscussionContent).html(result.view);
            })
    },
};

$(function () {
    DocumentHelper.init();
});