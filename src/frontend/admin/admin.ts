export {}

declare global {
    var ajaxurl: string
}
const $ = jQuery

$(document).on('ready', () => {
    $('#request_new_invoice').on('click', function (event) {
        event.preventDefault()

        const { orderId } = event.target.dataset

        if (!orderId) {
            throw new Error('Order id not present in dataset')
        }

        $.ajax({
            data: { action: 'request_new_invoice', orderId },
            type: 'post',
            url: globalThis.ajaxurl,
            success: function (data) {
                window.location.reload()
            },
        })
    })

    $('#request_delete_invoice').on('click', function (event) {
        event.preventDefault()

        const { orderId } = event.target.dataset

        if (!orderId) {
            throw new Error('Order id not present in dataset')
        }

        if (confirm('Opravdu vymazat?')) {
            $.ajax({
                data: { action: 'request_delete_invoice', orderId },
                type: 'post',
                url: globalThis.ajaxurl,
                success: function (data) {
                    window.location.reload()
                },
            })
        }
    })
})
