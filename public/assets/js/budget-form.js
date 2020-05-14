let $typeRadios = $('.js_budget-type');
$btns = $('.js_budget-type_btn');


// add active class when edit form loaded
if ($typeRadios.find(':checked').length) {
    $btns.each(function (i,e) {
        if ( $(e).data('type') === $typeRadios.find(':checked').val() )
            $(e).addClass('active');
    });
}

$btns.on('click', function (event) {
    const $this = $(this);
    // console.log(this);

    // toggle action class
    $btns.removeClass('active');
    $this.addClass('active');

    // uncheck all real radios
    $typeRadios.find(':checked').prop('checked', false);

    // find and set checked radio
    $typeRadios.find('[value="'+$this.data('type')+'"]').prop('checked', true);
});

// validate pseudo radios
let $btnPopovers = $('.js_budget-type_btn-wrapper').popover({content: "Select one of these fields", placement: 'bottom', trigger: 'manual'})
$('form[name="budget_form"] [type="submit"]').on('click', function () {
    if (!$typeRadios.find(':checked').length) {
        console.log('need to validate');
        $btnPopovers.popover('show');
    }
});