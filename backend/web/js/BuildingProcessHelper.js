'use strict';

var BuildingProcessHelper = {
    url: {
        discussionDocumentCreate: '/buildingprocess/deal/discussion-document-create',
        deleteDiscussionMessage: '/buildingprocess/deal/delete-discussion-message',
        discussionUpdate: '/buildingprocess/deal/discussion-update',
        dealCreate: '/buildingprocess/deal/create',
    },

    selectors: {
        stagesModal: '#stages-modal',
        closeModal: '.cancel-modal',
        openModal: 'button.stages-button',
        addOptionField: '.add-option-field',
        showTableStages: '#show_table_stages',
        stageGrid: '#stages',
        fieldGrid: '#field',
        stagesModel: '#stages-modal',
        assingModel: '#assing-modal',
        fieldModel: '#field-modal',
        fieldName: 'input.field-name',
        fieldDoc: 'input.field-doc',
        addStages: 'a.add-stages',
        addField: 'a.add-field',
        footerDealSing: '.footer-deal-sing',
        approve: '.approve',
        closeModel: 'a.cancel-modal',
        openModelStages: 'button.stages-button',
        openModelField: 'button.field-button',
        openModelAssing: 'button.assing-button',
        openModelAssingClient: 'button.assing-button-client',
        formDeal: 'form.menu-form',
        deleteOptionField: '.delete-option-field',
        deleteFieldCopy: '.delete-field',
        typeFieldLink: 'select.select-type-field',
        optionField: '.option-field',
        fields: '.fields',
        selectProcessId: '.selectProcessId',
        stagesCreate: '#stagesCreate',
        formAssing: '#formAssing',
        formStages: '#formStages',
        formField: '#formField',
        formSaving: '#formSaving',
        stageShow: '#stagesShow',
        discussionShow: '.footer-deal-add-discussion',
        discussionSaveButton: '.js-discussion-save',
        deleteDiscussionMessageButton: '.js-delete-discussion-message',
        activeStageLiElement: '#js-deal-stages li.active',
        stageLiElement: '#js-deal-stages li',
        modelDocumentId: '#modelDocumentId',
        processId: '#js-process-id',
        parentFolderId: '#js-parent-folder-id',
        dynamicDiscussionContent: '.js-dynamic-discussion-content',
        discussionForm: '.js-discussion-form',
        processInstId:'.js-process-instance-id',
        parentId: '.js-parent-document-id',
        linkPopupOpen: '.link-popup-open',
        modalDocument: '#document-modal',
        modalBodyDocument: 'div .document-body',
        documentButton: '.document-button',
        linkPopupClose: '.link-popup-close',
        docPI: '.doc-p-i',
        documentID: '#document-id',
        documentName: '#document-name',
        dealDocumentName : '.deal-document-name',
        dealDocumentID : '.deal-document-id',
        client_btn : '.client_btn',
        tableSortFiled: '.sortable-table-filed tbody',
        processFieldTemplateModify: '#processfieldtemplate-modify',
        processFieldTemplateRequired: '#processfieldtemplate-required',
        formPermission: 'form.permission'
    },

    elements: {},

    initElements: function () {
        var $this = this;
        $this.elements = {
            formStages: $($this.selectors.formStages),
            formField: $($this.selectors.formField),
            formSaving: $($this.selectors.formSaving),
            formAssing: $($this.selectors.formAssing),
            stagesShow: $($this.selectors.stageShow),
            selectProcess: $($this.selectors.selectProcessId),
            field: $($this.selectors.fields),
            formDeals: $($this.selectors.formDeal),
            discussionShow: $($this.selectors.discussionShow),
            discussionSaveButton: $($this.selectors.discussionSaveButton),
            deleteDiscussionMessageButton: $($this.selectors.deleteDiscussionMessageButton),
            linkPopupOpen: $($this.selectors.linkPopupOpen),
            modalBodyDocument: $($this.selectors.modalBodyDocument),
            documentButton: $($this.selectors.documentButton),
            docPI: $($this.selectors.docPI),
            linkPopupClose: $($this.selectors.linkPopupClose),
            modalDocument : $($this.selectors.modalDocument),
        }
    },

    init: function () {
        var $this = this;
        $this.initElements();

        $this.elements.selectProcess.on('change', function () {
            return $this.activeProcess();
        });
        $this.elements.formStages.on('pjax:success', function (data, status, xhr, options) {
            $this.onFormStages(data);
            $($this.selectors.closeModal).on('click', function () {
                event.preventDefault();
                $this.closeModalStage()
            });
        });
        $this.elements.formField.on('pjax:success', function (data, status, xhr, options) {
            $($this.selectors.deleteOptionField).on('click', function () {
                return $this.addOptionDelete(this)
            });
            $this.onFormField(data);
            $($this.selectors.typeFieldLink).on('change', function () {
                return $this.activeField()
            });
            $($this.selectors.addOptionField).on('click', function () {
                return $this.addOptionAdd()
            });
            $($this.selectors.closeModal).on('click', function () {
                event.preventDefault();
                $this.closeModalField()
            });
            $($this.selectors.processFieldTemplateModify).on('click', function () {
                $($this.selectors.processFieldTemplateRequired).prop('checked', false);
            });
            $($this.selectors.processFieldTemplateRequired).on('click', function () {
                $($this.selectors.processFieldTemplateModify).prop('checked', false);
            });
            $('.optionclient').find('#optionfield-name').on('change', function () {
                var value = $('.optionclient').find('#optionfield-name').val();
                if (value !== '') {
                    $('#processfieldtemplate-name').val('Client-' + value);
                } else {
                    $('#processfieldtemplate-name').val('');
                }
            })
        });
        $($this.selectors.client_btn).on('click', function () {
            $($this.selectors.openModelAssingClient).click()
        })

        $($this.selectors.discussionShow).on('click', function () {
           var panel = $('#js-deal-stages').find('li.active');
           panel.removeClass('active');
           var disscusion = $('#js-deal-stages').find("a[href='#discussion']").parent();
           disscusion.addClass('active');
        });
        $('.footer-deal-save').on('click', function () {
            $('.client_field').removeAttr('disabled');
        })
        $this.elements.formSaving.on('pjax:success', function (data, status, xhr, options) {

            $($this.selectors.approve).on('click', function () {
                $('.client_field').removeAttr('disabled');
                $(this).css({"background-color" : 'rgba(153, 204, 102, 1)'});
            });

            $($this.selectors.formPermission).submit(function(){
                $(":submit", this).attr("disabled", "disabled");
            });

            $($this.selectors.footerDealSing).on('click', function () {
                var form = $('.menu-form').find('.required').find('[name ^= Field]');
                var count = 0;
                $.each(form, function (item, value) {
                    if (value.value === "") {
                        count++;
                        var label = $("#" + value.id).parent().parent().find('label').text();
                        if (value.id === 'link') {
                            label = $("#" + value.id).parent().parent().parent().find('label').text();
                        }
                        $("#" + value.id).parent().find('.text-danger').text(label + ' cannot be blank.');
                    }
                });
                if (count == 0) {
                    $this.addNewDiscussionFile();
                    $($this.selectors.openModelAssing).click();
                }

            });
            $('#formSaving').on('click', '.link-popup-open', function () {
                $this.linkFieldPopup();
            })
            $($this.selectors.discussionShow).on('click', function () {
                $this.addNewDiscussionFile();
            });

            $this.closeModalAssing()
        });

        $this.elements.stagesShow.on('click', function () {
            $this.stagesShowOpen();
            $this.closeModalStage()
        });

        $this.elements.discussionSaveButton.on('click', function () {
            $this.showNewDiscussionMessage();
        });

        $this.elements.deleteDiscussionMessageButton.on('click', function () {
            var messageId = $(this).attr('id');
            $this.deleteDiscussionMessage(messageId);
        });

        $this.elements.linkPopupOpen.on('click', function () {
            $this.linkFieldPopup();
        });


        $this.elements.docPI.on('click', function () {
            var docId = $('.kv-focussed').parent().parent().attr('data-key'),
                docName = $('.kv-focussed').find('span.kv-node-label').text(),
                a = $('.link-popup-open').parent().parent().find('a'),
                link = $('.link-popup-open').parent().parent().find('input'),
                danger = $('.link-popup-open').parent().parent().find('.text-danger');
            a.attr('href', '/document?id=' + docId);
            link.val(docId);
            a.text(docName);
            danger.text('');
            $this.linkPopupClose();
        });

        $this.elements.linkPopupClose.on('click', function () {
            $this.linkPopupClose();
        });

    },

    onFormStages: function (data) {
        var $this = this;
        $this.elements.formStages.removeClass('hidden');
        if (data.currentTarget.textContent === 'successful') {
            $this.elements.formStages.addClass('hidden');
            $.pjax.reload({container: $this.selectors.stageGrid});
            data.currentTarget.textContent;
            return $this.closeModalStage();
        }
        $($this.selectors.openModal).click();
    },

    onFormField: function (data) {
        var $this = this;

        $this.elements.formField.removeClass('hidden');
        if (data.currentTarget.textContent === 'success') {
            $this.elements.formField.addClass('hidden');
            $.pjax.reload({container: "#field"});
            return $this.closeModalField();
        }
        $($this.selectors.openModelField).click();
    },

    clickAddStages: function () {
        var $this = this;
        $($this.selectors.openModelStages).click();
    },

    closeModalStage: function () {
        var $this = this;
        $($this.selectors.stagesCreate).empty();
        $($this.selectors.stagesModal).modal('hide');
    },

    closeModalField: function () {
        var $this = this;
        $($this.selectors.fieldModel).modal('hide');
    },

    closeModalAssing: function () {
        var $this = this;
        $($this.selectors.assingModel).modal('hide');
    },

    activeField: function () {
        var $this = this;
        $.ajax({
            url: '/field/option-field',
            type: 'get',
            data: {id: $($this.selectors.typeFieldLink).val()},
            dataType: 'json',
            cache: false,
            success: function (json) {
                if (json) {
                    if (json == 'client') {
                        $('.optionfield.select').remove();
                    } else {
                        $('.optionclient').remove()
                    }
                    return $($this.selectors.optionField).removeClass('hidden');
                }
                return $($this.selectors.optionField).addClass('hidden');
            }
        });
    },

    activeProcess: function () {
        var $this = this;
        $.ajax({
            url: '/buildingprocess/deal/view-process',
            type: 'get',
            data: {id: $($this.selectors.selectProcessId).val()},
            cache: false,
            success: function (form) {
                $this.elements.field.html(form.view);
                $this.elements.field.find('input.date').datepicker();
                $($this.selectors.fieldName).on('change', function () {
                    return $this.recordNameChange(this);
                });
                $this.validationDeals(form.validation);
            }
        });
    },

    recordNameChange: function (element) {
        var $this = this,
            name = $(element).val();

        $($this.selectors.fieldDoc).val(name);

        return true;
    },

    validationDeals: function (validation) {
        var $this = this;
        $.each(validation, function (index, item) {
            $('form.menu-form').yiiActiveForm('add', {
                id: item.id,
                name: item.name,
                container: item.container,
                input: item.input,
                error: '.text-danger',
                validateMethod: item.validate,
                validate: function (attribute, value, messages, deferred, $form) {
                    $.each(attribute.validateMethod, function (index, item) {
                        if (item.messages.pattern) {
                            item.messages.pattern = new RegExp(item.messages.pattern);
                        }
                        yii.validation[item.type](value, messages, item.messages);
                    });
                },
            });
        })
    },

    addOptionAdd: function () {
        var $this = this;
        var option = $('.optionfield');
        $($this.selectors.optionField).append('<div class="form-group delete-field">' + option.html() + '</div>');
        $($this.selectors.deleteFieldCopy).find('buttom').removeClass('btn-primary add-option-field').addClass('btn-danger delete-option-field');
        $($this.selectors.deleteFieldCopy).find('i').removeClass('glyphicon-plus').addClass('glyphicon-trash');
        $($this.selectors.deleteOptionField).on('click', function () {
            return $this.addOptionDelete(this)
        });
    },

    addOptionDelete: function (list) {
        $(list).parents('.form-group').remove();
    },

    stagesShowOpen: function () {
        var $this = this;
        $($this.selectors.showTableStages).toggle();
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
                $($this.selectors.deleteDiscussionMessageButton).on('click', function () {
                    var messageId = $(this).attr('id');
                    $this.deleteDiscussionMessage(messageId);
                })
            })

    },
    /**
     *
     * @param messageId
     */
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
                $($this.selectors.deleteDiscussionMessageButton).on('click', function () {
                    var messageId = $(this).attr('id');
                    $this.deleteDiscussionMessage(messageId);
                })
            })
    },

    addNewDiscussionFile: function () {
        var $this = this, data = {
            processInstId: $($this.selectors.processInstId).val(),
            parentId:  $($this.selectors.parentId).val(),
            parentFolderId: $($this.selectors.parentFolderId).val(),
            processId: $($this.selectors.processId).val(),

        };
        $.ajax({
            url: $this.url.discussionDocumentCreate,
            type: 'POST',
            dataType: 'json',
            data: data,
        })
            .done(function (result) {
                $this.activateLastElement();
                $($this.selectors.stageLiElement).last().show();
                $($this.selectors.modelDocumentId).val(result.id);
            })
    },

    activateLastElement: function () {
        var $this = this, activateElement = $($this.selectors.activeStageLiElement),
            lastElement = $($this.selectors.stageLiElement).last();
        activateElement.removeClass('active');
        lastElement.addClass('active');
    },

    linkFieldPopup: function () {
        var $this = this;
        $this.elements.documentButton.click();
        $.ajax({
            url: '/buildingprocess/deal/document',
            cache: false,
            success: function (form) {
                $this.elements.modalBodyDocument.html(form);
            }
        });
    },
    linkPopupClose: function () {
        var $this = this;
        $this.elements.modalBodyDocument.empty();
        $this.elements.modalDocument.modal('hide');
    },

    tableSortFiled: function () {
        var $this = this;
        $($this.selectors.tableSortFiled).sortable({
            update: function (event, ui) {
                var sortedList = $($this.selectors.tableSortFiled).sortable('toArray').toString();
                $.ajax({
                    url: '/field/sort',
                    type: 'POST',
                    data: {sortedList: sortedList}
                });
            }
        }).disableSelection();
    }

};


$(function () {
    BuildingProcessHelper.init();
});
