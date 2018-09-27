$(function ($) {

    $(document).on('click', '#btn-rate',function (e) {
        e.preventDefault();
        var data = {
            action: 'rate',
            id_post: $('input[name=id_post]').val(),
            id_user: $('input[name=id_user]').val(),
            price: $('input[name=price]').val()
        };
        console.log(TendAjax.ajax_url);
        $.post(TendAjax.ajax_url, data, function (result) {
            alert(result.success);
        }, 'json');

    });

    function find_page_number( element ) {
        element.find('span').remove();
        return parseInt( element.html() );
    }

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        page = find_page_number( $(this).clone() );

        $.ajax({
            url: TendAjax.ajax_url,
            type: 'post',
            data: {
                action: 'ajax_pagination',
                query_vars: TendAjax.query_vars,
                page: page
            },
            success: function( html ) {
                $('#main').find( '.tender-card' ).remove();
                $('#main').find( 'nav' ).remove();
                $('#main nav').remove();
                $('#main').append( html );
            }
        })

    })
});