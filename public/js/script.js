$(function () {
    $(".cart .quantity").change((e) => {
        $(".quantity").parent('form').submit()
    })
})