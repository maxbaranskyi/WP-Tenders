jQuery(function ($) {

    $(document).on('click', '#btn-rate',function (e) {
        e.preventDefault();
        var data = {
            action: 'rate',
            id_post: $('input[name=id_post]').val(),
            id_user: $('input[name=id_user]').val(),
            price: $('input[name=price]').val(),
            nonce: $('#_wpnonce').val()
        };
        console.log(TendAjax.ajax_url);
        $.post(TendAjax.ajax_url, data, function (result) {
            alert(result.success);
        }, 'json');

    });

    function change_page(clicked) {
        let page_link = $('a.page-numbers')[0];
        page_link = page_link.toString();
        let ready_link = page_link.split('/');

        let span_page = $('span.current').text();
        ready_link[ready_link.length - 2] = span_page;

        $('span.current').replaceWith(`<a class="page-numbers" href="${ready_link.join('/')}">${span_page}</a>`);
        $(clicked).replaceWith(`<span aria-current="page" class="page-numbers current">${$(clicked).text()}</span>`);
    }

    function find_page_number( element ) {
        element.find('span').remove();
        return parseInt( element.html() );
    }

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        page = find_page_number( $(this).clone() );
        let content = wp.template("content-template");

        let clicked = this;
        $.ajax({
            url: TendAjax.ajax_url,
            type: 'post',
            data: {
                action: 'ajax_pagination',
                query_vars: TendAjax.query_vars,
                page: page
            },
            success: function( result ) {
                $('#content .tender-card' ).remove();
                if (result.success === false) {
                    alert("POSTS ERROR");
                    return 1;
                }

                for(let post of result.data) {
                    let tenderItemHtml = content({post: post, terms: post.temrs[0]});
                    $('#content').append(tenderItemHtml);
                }

                change_page(clicked);
            }
        })
    })
});
