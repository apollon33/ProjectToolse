var UserHelper = {
    map: {},

    _map: function() {
        var $this = this;
        $this.map = {
            body: $('body'),
            formCampaign: $('#formCampaign'),
            formCampaign_r:$('#formCampaign-registration'),
            formCampaign_s:$('#formCampaign-salary'),
            formVacation:$('#formCampaign-vacation'),
            vacationForm: '#vacationForm',
            vacation: $('#vacation'),
            idVacation: '#vacation',
            closeModal: '.cancel-modal',
            openModalVacation: 'button.campaign-button-vacation',
            formMultiEmail: $('#formMultiEmail'),
            openMultiEmail_button: 'button.multiEmail-button',
            formTrash:$('#formTrash'),
            trashForm:$('#trashForm'),
            actionAllLinkTrash: 'a.action-list-trash',
            actionAllLinkUpdateTrash:'a.action-list-allupdate-trash',
            url:'/user/',
            pageSizeTrash: 'select#page-size-trash',
            gridElementTrash: '.grid-view-trash',
            positionCreate:'#positionCreate',
            registrationCreate:'#registrationCreate',
            salaryCreate:'#salaryCreate',
            position:$('.id_position'),
            registration:$('.registration'),
            salary:$('.salary'),
            campaign_modal_vacation:$('#campaign-modal-vacation'),
            multiEmail_modal: $('#multiEmail-modal'),
            campaign_modal:$('#campaign-modal'),
            campaign_modal_r:$('#campaign-modal-registration'),
            campaign_modal_s:$('#campaign-modal-salary'),
            campaign_modal_trash:$('#campaign-modal-trash'),
            positionSave:'.positionSave',
            registrationSave:'.registrationSave',
            salarySave:'.salarySave',
            positionSdow:$('#positionSdow'),
            registrationSdow:$('#registrationSdow'),
            salarySdow:$('#salarySdow'),
            createPosition:$('#createPosition'),
            createRegistration:$('#createRegistration'),
            createSalary:$('#createSalary'),
            createUser:$('#createUser'),
            calendar_pickerinterview:$('#date-pickerinterview'),
            user_interview:$('#user-interview'),
            date_picker:$("#date-picker,#date-picker1,#date-pickerdate_receipt"),
            input_date_picker:$("#logposition-created_at,#logregistration-created_at,#user-date_receipt"),
            trash:'.trash',
            calendarFormCancel:'.cancel-modal',
            visibleModelVacation: '.visible-model-vacation',
            showAllVacations:'.js-show-all-vacations',
            allVacations:'.js-all-vacations',
            copySkill: '.copy-skill',

        };
    },

    init: function() {
        var $this = this;
        $this._map();
        $this.map.formCampaign.on('pjax:success', function(data, status, xhr, options) {
            $this.onFormCampaignResponse(data);
            $($this.map.positionSave).on('click',function(){
                $this.validationSaveModal($this.map.positionCreate, $this.map.campaign_modal);
                $this.positionSaveModal();
            });
            $($this.map.calendarFormCancel).on('click', function(){event.preventDefault();$this.closePositionModal()});
        });

        $this.map.formCampaign_r.on('pjax:success', function(data, status, xhr, options) {
            $this.onFormCampaignResponseRegistration(data);
            $($this.map.registrationSave).on('click',function(){
                $this.validationSaveModal($this.map.registrationCreate, $this.map.campaign_modal_r);
                $this.registrationSaveModal();
            });
            $($this.map.calendarFormCancel).on('click', function(){event.preventDefault();$this.closeRegistrationModal()});
        });


        $this.map.formTrash.on('pjax:success', function(data, status, xhr, options) {
            $this.onFormTrash(data,this);
            $($this.map.pageSizeTrash).on('click',function() { return $this.pageSizeTrashChange(this); });
            $($this.map.actionAllLinkTrash).on('click',function() { return $this.actionAllLinkTrashClick(this); });
            $($this.map.actionAllLinkUpdateTrash).on('click',function() { return $this.actionAllLinkUpdaterashClick(this); });

            /*$($this.map.registrationSave).on('click',function(){
                $this.validationSaveModal($this.map.registrationCreate, $this.map.campaign_modal_r);
                $this.registrationSaveModal();
            });*/
            $($this.map.calendarFormCancel).on('click', function(){event.preventDefault();$this.closeRegistrationModal()});
        });

        $this.map.formCampaign_s.on('pjax:success', function(data, status, xhr, options) {
            $this.onFormCampaignResponseSalary(data);
            $($this.map.salarySave).on('click',function(){
                $this.validationSaveModal($this.map.salaryCreate, $this.map.campaign_modal_s);
                $this.salarySaveModal();
            });
            $($this.map.calendarFormCancel).on('click', function(){event.preventDefault();$this.closeSalaryModal()});
        });

        $this.map.formVacation.on('pjax:success', function (data, status, xhr, options) {
            $this.onFormVacation(data);
            $($this.map.closeModal).on('click', function () {
                event.preventDefault();
                $this.closeModalVacation()
            });
        });

        $this.map.formMultiEmail.on('pjax:success', function (data, status, xhr, options) {
            $this.onFormMultiEmail(data);
            $($this.map.closeModal).on('click', function () {
                event.preventDefault();
                $this.closeModalMultiEmail()
            });
        });

        $this.map.body.on('click', $this.map.visibleModelVacation, function () {
            return $this.visibleModel()
        });

        $this.map.body.on('click', $this.map.copySkill, function () {
            $this.copySkill(this);
        });

        $($this.map.trash).on('click',function(){$this.campaignTrash();})

        $this.map.position.on('click',function(){$this.campaignPosition();})
        $this.map.salary.on('click',function(){$this.campaignSalary();})
        $this.map.registration.on('click',function(){$this.campaignRegistration();})
        $this.map.positionSdow.on('click',function(){$this.campaignPositionShowOpen();})
        $this.map.salarySdow.on('click',function(){$this.campaignSalaryShowOpen();})
        $this.map.registrationSdow.on('click',function(){$this.campaignRegistrationShowOpen();})
        $this.map.createUser.on('click',function(){$this.createUserForm(event);})
        $this.map.calendar_pickerinterview.on('change',function () {return $this.activePickerinterview()});
        $("#campaign-modal,#campaign-modal-registration,#campaign-modal-salary").draggable({handle: ".modal-header"});
        $this.map.body.on('click', $this.map.showAllVacations, function () {
            if ($($this.map.allVacations).hasClass('hidden')) {
                $($this.map.allVacations).removeClass('hidden');
            } else {
                $($this.map.allVacations).addClass('hidden');
            }
        })
    },

    copySkill: function (list) {
        var fieldCopy = $(list).parents('.copy-field').find('input').val(),
            temp = $("<input>");
        $(list).addClass('copied');
        setTimeout(
            function (list) {
                $('.copied').removeClass('copied');
            },
            1500);
        $(list).parents('.copy-field').append(temp);
        temp.val(fieldCopy).select();
        document.execCommand("copy");
        temp.remove();
    },

    visibleModel:function () {
        var $this=this;
        $($this.map.openModalVacation).click();
    },

    closeModalVacation: function () {
        var $this = this;
        $this.map.formVacation.addClass('hidden');
        $this.map.campaign_modal_vacation.modal('hide');
    },

    onFormVacation: function (data) {
        var $this = this;
        $this.map.formVacation.removeClass('hidden');

        var date = $(data.currentTarget);
        var complete = date.find($this.map.vacationForm);
        if (!$(complete).get(0)) {
            $this.map.vacation.html($(data.currentTarget).html());
            return $this.closeModalVacation();
        }
    },

    closeModalMultiEmail: function () {
        var $this = this;
        $this.map.formMultiEmail.addClass('hidden');
        $this.map.multiEmail_modal.modal('hide');
    },

    onFormMultiEmail: function (data) {
        var $this = this;
        $this.map.formMultiEmail.removeClass('hidden');

        if(data.currentTarget.textContent==='complete'){
            $.pjax.reload({container:"#multiEmail"});
            return $this.closeModalMultiEmail();
        }
        $($this.map.openMultiEmail_button).click();

    },

    activePickerinterview:function () {
        var $this=this;
        var userInterview=$this.map.user_interview.val();
        var userPickerinterview=$this.map.calendar_pickerinterview.val();
        $this.map.date_picker.val(userPickerinterview);
        $this.map.input_date_picker.val(userInterview);
    },

    onFormCampaignResponse: function(data) {
        var $this = this;
        $this.map.formCampaign.removeClass('hidden');
        if(data.currentTarget.textContent==='complete'){
            $.pjax.reload({container:"#notes"});
            $this.closePositionModal();
        }
    },

    closePositionModal: function() {
        var $this = this;
        $this.map.formCampaign.addClass('hidden');
        $this.map.campaign_modal.modal('hide');
    },

    positionSaveModal:function(){
        var $this = this;
        $('#user-position_id').val($('#logposition-position_id option:selected').val());
    },

    campaignPosition: function(){
        $('#show_registration,#show_salary,#show_position').css('display','none');
        $('button.campaign-button').click();
    },
    campaignPositionShowOpen: function(){
        if	( $('#show_position').css('display') !== 'none')
            $('#show_position').css('display','none');
        else{
            $('#show_registration,#show_salary').css('display','none');
            $('#show_position').css('display','block');
        }
    },

    onFormCampaignResponseRegistration: function(data) {
        var $this = this;
        $this.map.formCampaign_r.removeClass('hidden');
        if(data.currentTarget.textContent==='complete'){
            $.pjax.reload({container:"#notes-registration"});
            $this.closeRegistrationModal();
        }
    },

    closeRegistrationModal: function() {
        var $this = this;
        $this.map.formCampaign_r.addClass('hidden');
        $this.map.campaign_modal_r.modal('hide');
    },

    registrationSaveModal:function(){
        var $this = this;
        $('#user-registration_id').val($('#logregistration-registration_id option:selected').val());
        $('#logregistration-company_id').val($('#logregistration-company_id option:selected').val());
    },

    campaignRegistration: function(){

        $('#show_registration,#show_salary,#show_position').css('display','none');
        $('button.campaign-button-registration').click();
    },
    campaignRegistrationShowOpen: function(){
        if	( $('#show_registration').css('display') !== 'none')
            $('#show_registration').css('display','none');
        else{
            $('#show_position,#show_salary').css('display','none');
            $('#show_registration').css('display','block');
        }
    },

    onFormCampaignResponseSalary: function(data) {
        var $this = this;
        $this.map.formCampaign_s.removeClass('hidden');
        if(data.currentTarget.textContent==='complete'){
            $.pjax.reload({container:"#notes-salary"});
            $this.closeSalaryModal();
        }
    },

    closeSalaryModal: function() {
        var $this = this;
        $this.map.formCampaign_s.addClass('hidden');
        $this.map.campaign_modal_s.modal('hide');
    },

    salarySaveModal:function(){
        var $this = this;
        $('#user-salary').val($('#logsalary-salary').val()+' '+$('#logsalary-currency option:selected').text() );
        $('#user-reporting_salary').val($('#logsalary-reporting_salary').val()+' '+$('#logsalary-currency option:selected').text() );
    },

    campaignSalary: function(){
        $('#show_registration,#show_salary,#show_position').css('display','none');
        $('button.campaign-button-salary').click();
    },
    campaignSalaryShowOpen: function(){
        if	( $('#show_salary').css('display') !== 'none')
            $('#show_salary').css('display','none');
        else{
            $('#show_position,#show_registration').css('display','none');
            $('#show_salary').css('display','block');
        }
    },

    validationSaveModal:function(forms,model){
        var $this = this;

        var $form =$(forms),
            data = $form.data("yiiActiveForm");

        $.each(data.attributes, function() {
            this.status = 3;
        });

        $form.yiiActiveForm('remove', 'description');
        $form.yiiActiveForm("validate");

        if ($form.find('.has-error').length<=1)
            model.modal('hide');
        else
            event.preventDefault();

    },

    campaignTrash:function(){
        $('button.campaign-button-trash').click();
    },

    onFormTrash:function (data,link) {
        var $this = this;
        $this.map.formTrash.removeClass('hidden');
        if(data.currentTarget.textContent==='complete'){
            $.pjax.reload({container:"#trash"});
            $this.closeTrashModal();
        }
        if($($this.map.trashForm.selector).text()==='complete'){
            $.pjax.reload({container:"#trash"});
            $this.closeTrashModal();
        }

    },

    pageSizeTrashChange: function(element) {

        var $this = this,
            form = $(element).parents('form');

        var url = form.attr('action');

        $.pjax({
            url: url,
            type: 'POST',
            container: $this.map.trashForm.selector,
            data: {params: $(element).val()},
            push: false,
        });

        return false;
    },

    actionAllLinkTrashClick:function (link) {
        var $this = this,
            href = $(link).attr('href'),
            message = $(link).data('confirm'),
            ids = [];

        if ($($this.map.gridElementTrash).length > 0) {
            ids = $($this.map.gridElementTrash).yiiGridView('getSelectedRows');
        } else {
            $this.map.gridSortable.find($this.map.checkedIds).each(function () {
                ids.push($(this).val());
            });
        }

        if (message != undefined && message != '' && !confirm(message)) {
            return false;
        }

        $.pjax({
            url: href,
            type: 'POST',
            container: $this.map.trashForm.selector,
            data: {ids: ids},
            push: false,
        });

        return false;
    },

    actionAllLinkUpdaterashClick:function (link) {
        var $this = this,
            href = $(link).attr('href'),
            message = $(link).data('confirm'),
            ids = [];

        if ($($this.map.gridElementTrash).length > 0) {
            ids = $($this.map.gridElementTrash).yiiGridView('getSelectedRows');
        } else {
            $this.map.gridSortable.find($this.map.checkedIds).each(function () {
                ids.push($(this).val());
            });
        }

        if (message != undefined && message != '' && !confirm(message)) {
            return false;
        }

        $.pjax({
            url: href,
            type: 'POST',
            container: $this.map.trashForm.selector,
            data: {ids: ids},
            push: false,
        });

        return false;
    },

    closeTrashModal: function() {
        var $this = this;
        $this.map.formTrash.addClass('hidden');
        $this.map.campaign_modal_trash.modal('hide');
    },

    createUserForm: function(event){

        var obj=$("#positionCreate,#registrationCreate,#salaryCreate").serializeArray();
        var event=[];
        $.each( obj, function( key, obj ) {
            event.push( obj.value);
        });
        $('#user-log').val( JSON.stringify(event));
    }
};

$(function() {
    UserHelper.init();
});
