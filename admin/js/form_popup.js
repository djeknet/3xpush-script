$(document).ready(function () {

    $('.link-button').each(function () {
       $(this).on('click', ajax_load_popup_form);
    });

    function showLoading() {
        hideLoading();
        $('#popup_form .modal-content').append('<div class="modal-loading"></div>');
    }

    function hideLoading() {
        $('#popup_form .modal-content .modal-loading').remove();
    }

    // загрузка формы
    function ajax_load_popup_form(e) {

        var id = $(this).data('id');
        var action = $(this).data('action');
        var target = $(this).data('target').replace('#', '');

        var blockModal = $('#' + target).find('#block-1');

        blockModal.empty();

        showLoading();

        $.ajax({
            url: 'ajax/form_popup.php',
            data: {
                id: id,
                action: action
            },
            success: function (response) {
                hideLoading();
                $('#' + target).find('#block-1').html(response);
            },
            error: function () {
                hideLoading();
            }
        });

    }

    $('#updateFormPopup').on('submit', function (e) {

        e.preventDefault();

        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var modal = $(this).closest('.modal');
        var id = modal.find('input[name=id]').val();

        var blockModal = modal.find('#block-1');

        if(!validationPopupForm()) {
            return false;
        }

        showLoading();

        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            success: function (response) {

                hideLoading();

                if(response == 'OK') {

                    modal.find(':input, textarea').each(function () {
                        var name = $(this).attr('name');
                        var value = $(this).val();
                        var updateFieldId = $('#' + name + '_field_update_' + id);

                        if($(updateFieldId).length > 0) {
                            $(updateFieldId).html(value);
                        }
                    });

                    blockModal.append('<div style="text-align: center"><img src=images/success.png></div>');

                    setTimeout(function () {
                        modal.modal("hide");
                    }, 1000);

                }

            },
            error: function (error) {
            }
        });

    });

    function validationPopupForm() {

        var form = $('#updateFormPopup');
        var cpc_price_min = form.find('#cpc_price').data('min');
        var cpc_price_max = form.find('#cpc_price').data('max');

        form.validate({
            rules: {
                cpc_price: {
                    required: true,
                    rangeOrZero: [cpc_price_min, cpc_price_max]
                },
            },
            messages: {
                cpc_price: "Min price " + cpc_price_min + ", max price " + cpc_price_max,
            },
        });

        return form.valid();

    }

});
